<?php

namespace App\Http\Controllers\landlord;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\landlord\Tenant;
use Illuminate\Support\Facades\Artisan;
use App\Models\GeneralSetting;
use App\Models\landlord\Package;
use App\Traits\TenantInfo;
use DB;
use App\Mail\TenantCreate;
use App\Models\MailSetting;
use Mail;
use Database\Seeders\Tenant\TenantDatabaseSeeder;
use Modules\Ecommerce\Database\Seeders\EcommerceDatabaseSeeder;

class ClientController extends Controller
{
    use TenantInfo;
    use \App\Traits\MailInfo;

    public function index()
    {
        if (cache()->has('general_setting')) {
            $general_setting = cache()->get('general_setting');
        } else {
            $general_setting = DB::table('general_settings')->latest()->first();
        }
        $lims_client_all = Tenant::all();
        $lims_package_all = Package::where('is_active', true)->get();
        return view('landlord.client.index', compact('lims_client_all', 'lims_package_all', 'general_setting'));
    }

    public function store(Request $request)
    {
        if (!env('USER_VERIFIED'))
            return redirect()->back()->with('not_permitted', 'This feature is disable for demo!');

        if (cache()->has('general_setting')) {
            $general_setting = cache()->get('general_setting');
        } else {
            $general_setting = DB::table('general_settings')->latest()->first();
        }

        $package = Package::select('is_free_trial', 'features')->find($request->package_id);
        $features = json_decode($package->features);
        $modules = [];
        if (in_array('manufacturing', $features)) {
            $modules[] = 'manufacturing';
        }
        if (in_array('ecommerce', $features)) {
            $modules[] = 'ecommerce';
        }
        if (in_array('woocommerce', $features))
            $modules[] = 'woocommerce';
        if (count($modules))
            $modules = implode(",", $modules);
        else
            $modules = Null;

        if ($request->subscription_type == 'free')
            $numberOfDaysToExpired = $general_setting->free_trial_limit;
        elseif ($request->subscription_type == 'monthly')
            $numberOfDaysToExpired = 30;
        elseif ($request->subscription_type == 'yearly')
            $numberOfDaysToExpired = 365;

        //creating tenant
        $tenant = Tenant::create(['id' => $request->tenant]);
        $tenant->domains()->create(['domain' => $request->tenant . '.' . env('CENTRAL_DOMAIN')]);

        //Start set tenant specific data for TenantDatabaseSeeder
        $packageData = Package::find($request->package_id);
        $pack_perm_role_pairs = explode('),(', trim($packageData->role_permission_values, '()'));
        // Convert each pair into an associative array
        if ($pack_perm_role_pairs != [""]) {
            $package_permissions_role = array_map(function ($pk_perm_role_p) {
                [$permission_id, $role_id] = explode(',', $pk_perm_role_p); // Split the pair
                return [
                    'permission_id' => (int) $permission_id, // Cast to int
                    'role_id' => (int) $role_id,             // Cast to int
                ];
            }, $pack_perm_role_pairs);
        } else {
            $package_permissions_role = [];
        }

        $tenantData = [
            //set general_setting information
            'site_title' => $general_setting->site_title,
            'site_logo' => $general_setting->site_logo,
            'package_id' => $request->package_id,
            'subscription_type' => $request->subscription_type,
            'developed_by' => $general_setting->developed_by,
            'modules' => $modules,
            'expiry_date' => date("Y-m-d", strtotime("+" . $numberOfDaysToExpired . " days")),
            //set user information
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'phone' => $request->phone_number,
            'company_name' => $request->company_name,
            //set permission info
            'package_permissions_role' => $package_permissions_role,
        ];
        //End set tenant specific data for TenantDatabaseSeeder and call running TenantDatabaseSeeder

        //Start running TenantDatabaseSeeder
        TenantDatabaseSeeder::$tenantData = $tenantData;
        Artisan::call('tenants:seed', [
            '--tenants' => $request->tenant,
            '--force' => true,
        ]);
        //End running TenantDatabaseSeeder

        copy(public_path("landlord/images/logo/") . $general_setting->site_logo, public_path("logo/") . $general_setting->site_logo);

        //Start running Ecommerce migration and seeder for tenant if package has ecommerce module
        if (isset($modules) && str_contains($modules, "ecommerce")) {
            Artisan::call('tenants:migrate', [
                '--tenants' => $request->tenant,
                '--path' => base_path('Modules/Ecommerce/Database/Migrations'),
                '--force' => true,
            ]);
            Artisan::call('tenants:seed', [
                '--tenants' => $request->tenant,
                '--class' => EcommerceDatabaseSeeder::class,
                '--force' => true,
            ]);

            //Update slug column on category,brand,product table as this is needed for ecommerce
            $tenant->run(function () {
                $this->brandSlug();
                $this->categorySlug();
                $this->productSlug();

                DB::table('categories')->whereIn('id', [1, 6, 12, 23, 29, 30, 31, 33, 39])->update([
                    'icon' => DB::raw("
                        CASE
                            WHEN id = 1 THEN '20240117121500.png'
                            WHEN id = 6 THEN '20240117121330.png'
                            WHEN id = 12 THEN '20240117121400.png'
                            WHEN id = 23 THEN '20240117121523.png'
                            WHEN id = 29 THEN '20240117121304.png'
                            WHEN id = 30 THEN '20240117121238.png'
                            WHEN id = 31 THEN '20240117122452.png'
                            WHEN id = 33 THEN '20240117121224.png'
                            WHEN id = 39 THEN '20240204050037.png'
                        END
                    ")
                ]);

                DB::table('products')->update(['is_online' => 1]);
            });

            copy(public_path("logo/") . $general_setting->site_logo, public_path("frontend/images/") . $general_setting->site_logo);
        }
        //End running Ecommerce migration and seeder if package has ecommerce module


        if (!env('WILDCARD_SUBDOMAIN')) {
            $this->addSubdomain($tenant);
        }

        //updating tenant others information on landlord DB
        $tenant->update(['package_id' => $request->package_id, 'subscription_type' => $request->subscription_type, 'company_name' => $request->company_name, 'phone_number' => $request->phone_number, 'email' => $request->email, 'expiry_date' => date("Y-m-d", strtotime("+" . $numberOfDaysToExpired . " days"))]);


        $message = 'Client created successfully.';
        //sending welcome message to tenant
        $mail_setting = MailSetting::latest()->first();
        if ($mail_setting) {
            $this->setMailInfo($mail_setting);
            $mail_data['email'] = $request->email;
            $mail_data['company_name'] = $request->company_name;
            $mail_data['superadmin_company_name'] = $general_setting->site_title;
            $mail_data['subdomain'] = $request->tenant;
            $mail_data['name'] = $request->name;
            $mail_data['password'] = $request->password;
            $mail_data['superadmin_email'] = $general_setting->email;
            try {
                Mail::to($mail_data['email'])->send(new TenantCreate($mail_data));
            } catch (\Exception $e) {
                $message = 'Client created successfully. Please setup your <a href="mail_setting">mail setting</a> to send mail.';
            }
        }

        return redirect()->back()->with('message', $message);
    }

    public function addCustomDomain(Request $request)
    {
        //return $request;
        DB::table('domains')->insert([
            'domain' => $request->domain,
            'tenant_id' => $request->id,
            'created_at' => \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now()
        ]);
        return redirect()->back()->with('message', 'Custom domain created successfully');
    }

    public function renew(Request $request)
    {
        if (!env('USER_VERIFIED'))
            return redirect()->back()->with('not_permitted', 'This feature is disable for demo!');

        $expiry_date = $request->expiry_date;
        $subscription_type = $request->subscription_type;
        $tenant = Tenant::find($request->id);
        $tenant->update(['expiry_date' => date('Y-m-d', strtotime($expiry_date)), 'subscription_type' => $subscription_type]);
        $tenant->run(function () use ($expiry_date, $subscription_type) {
            GeneralSetting::latest()->first()->update(['expiry_date' => date('Y-m-d', strtotime($expiry_date)), 'subscription_type' => $subscription_type]);
        });
        return redirect()->back()->with('message', 'Subscription renewed successfully!');
    }

    public function changePackage(Request $request)
    {
        if (!env('USER_VERIFIED'))
            return redirect()->back()->with('not_permitted', 'This feature is disable for demo!');
        $packageData = Package::select('permission_id', 'features')->find($request->package_id);

        $features = json_decode($packageData->features);
        $modules = [];
        if (in_array('manufacturing', $features)) {
            $modules[] = 'manufacturing';
        }
        if (in_array('ecommerce', $features))
            $modules[] = 'ecommerce';
        if (in_array('woocommerce', $features))
            $modules[] = 'woocommerce';
        if (count($modules))
            $modules = implode(",", $modules);
        else
            $modules = Null;

        $abandoned_permission_ids = [];
        $permission_ids = [];
        $prev_permission_ids = [];

        if (!empty($packageData->permission_id)) {
            $permission_ids = explode(",", $packageData->permission_id);
        }

        $tenant = Tenant::find($request->client_id);
        $prevPackageData = Package::select('permission_id')->find($tenant->package_id);
        if (!empty($prevPackageData->permission_id)) {
            $prev_permission_ids = explode(",", $prevPackageData->permission_id);
        }

        //collecting permission ids which needs to be deleted
        foreach ($prev_permission_ids as $key => $prev_permission_id) {
            if (!in_array($prev_permission_id, $permission_ids)) {
                $abandoned_permission_ids[] = $prev_permission_id;
            }
        }
        //updating tenant package id in superadmin db
        $tenant->update(['package_id' => $request->package_id]);
        $this->changePermission($tenant, json_encode($abandoned_permission_ids), json_encode($permission_ids), $request->package_id, $modules);
        return redirect()->back()->with('message', 'Package changed successfully');
    }

    public function destroy($id)
    {
        if (!env('USER_VERIFIED'))
            return redirect()->back()->with('not_permitted', 'This feature is disable for demo!');
        $tenant = Tenant::find($id);
        foreach ($tenant->domains as $domain) {
            $domain->delete();
        }
        $tenant->delete();

        if (!env('WILDCARD_SUBDOMAIN')) {
            $this->deleteSubdomain($tenant);
        }

        return redirect()->back()->with('message', 'Client deleted successfully');
    }

    public function deleteBySelection(Request $request)
    {
        if (!env('USER_VERIFIED'))
            return redirect()->back()->with('not_permitted', 'This feature is disable for demo!');
        $clients_id = $request['clientsIdArray'];
        foreach ($clients_id as $id) {
            $tenant = Tenant::find($id);
            foreach ($tenant->domains as $domain) {
                $domain->delete();
            }
            $tenant->delete();

            if (!env('WILDCARD_SUBDOMAIN')) {
                $this->deleteSubdomain($tenant);
            }
        }
        return 'Clients deleted successfully';
    }
}
