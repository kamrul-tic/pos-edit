<?php

namespace App\Http\Controllers\landlord;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\landlord\Tenant;
use DB;
use Cache;
use App\Models\GeneralSetting;
use App\Traits\TenantInfo;
use Stripe\Stripe;
use App\Models\landlord\Package;
use App\Models\landlord\TenantPayment;
use App\Models\landlord\Page;
use App\Models\MailSetting;
use App\Models\Coupon;
use App\Mail\ContactForm;
use Mail;
use ZipArchive;
use Nwidart\Modules\Facades\Module;

class LandingPageController extends Controller
{
    use TenantInfo;
    use \App\Traits\CacheForget;
    use \App\Traits\MailInfo;

    public function contactForm(Request $request)
    {
        $mail_data = $request->all();
        //return $mail_data;
        $mail_setting = MailSetting::latest()->first();
        if($mail_data['email'] && $mail_setting) {
            $this->setMailInfo($mail_setting);
            try {
                Mail::to($mail_data['email'])->send(new ContactForm($mail_data));
                $message = 'Mail sent successfully';
            }
            catch(\Exception $e){
                $message = 'Mail not sent';
            }
        }
        else
            $message = 'Please setup your mail setting';
        return redirect()->back()->with('message', $message);
    }

    //This function is only for reset tenant database for demo and check cron job of salprosaas cpanel
    public function resetClientDB()
    {
        $tenants = Tenant::all();
        if (count($tenants)) {
            foreach ($tenants as $tenant) {
                $tenant->run(function () {
                    //clearing all the cached queries
                    $this->cacheForget('biller_list');
                    $this->cacheForget('brand_list');
                    $this->cacheForget('category_list');
                    $this->cacheForget('coupon_list');
                    $this->cacheForget('customer_list');
                    $this->cacheForget('customer_group_list');
                    $this->cacheForget('product_list');
                    $this->cacheForget('product_list_with_variant');
                    $this->cacheForget('warehouse_list');
                    $this->cacheForget('table_list');
                    $this->cacheForget('tax_list');
                    $this->cacheForget('currency');
                    $this->cacheForget('general_setting');
                    $this->cacheForget('pos_setting');
                    $this->cacheForget('user_role');
                    $this->cacheForget('permissions');
                    $this->cacheForget('role_has_permissions');
                    $this->cacheForget('role_has_permissions_list');
                    //clearing all data from the DB
                    DB::statement('SET FOREIGN_KEY_CHECKS=0;');
                    $tables = DB::select('SHOW TABLES');
                    $str = 'Tables_in_' . DB::getDatabaseName();
                    foreach ($tables as $table) {
                        DB::table($table->$str)->truncate();
                    }
                    //importing data from DB
                    $filePath = public_path(DB::getDatabaseName() . '.sql');
                    DB::unprepared(file_get_contents($filePath));
                });
            }
        }
    }

    public function index()
    {
        if(isset($_COOKIE['landing_page_language'])) {
            $lang_id = $_COOKIE['landing_page_language'];
        }
        else {
            $default_language = DB::table('languages')->where('is_default', true)->first();
            if($default_language)
                $lang_id = $default_language->id;
            else
                $lang_id = 1;
        }

        $present_lang = DB::table('languages')->find($lang_id);
        \App::setLocale($present_lang->code);

        $general_setting =  Cache::remember('general_setting', 60*60*24*365, function () {
            return DB::table('general_settings')->latest()->first();
        });

        $packages =  Cache::remember('packages', 60*60*24*365, function () {
            return DB::table('packages')->where('is_active', true)->get();
        });

        $tenant_signup_description =  Cache::remember('tenant_signup_descriptions', 60*60*24*365, function () use ($lang_id) {
            return DB::table('tenant_signup_descriptions')->where('lang_id', $lang_id)->first();
        });
        if(!$tenant_signup_description)
            $tenant_signup_description = DB::table('tenant_signup_descriptions')->where('lang_id', 1)->first();

        $hero =  Cache::remember('hero', 60*60*24*365, function () use ($lang_id) {
            return DB::table('heroes')->where('lang_id', $lang_id)->first();
        });
        if(!$hero)
            $hero = DB::table('heroes')->where('lang_id', 1)->first();

        $faq_description =  Cache::remember('faq_descriptions', 60*60*24*365, function () use ($lang_id) {
            return DB::table('faq_descriptions')->where('lang_id', $lang_id)->first();
        });
        if(!$faq_description)
            $faq_description = DB::table('faq_descriptions')->where('lang_id', 1)->first();

        $faqs =  Cache::remember('faqs', 60*60*24*365, function () {
            return DB::table('faqs')->orderBy('order', 'asc')->get();
        });

        $module_description =  Cache::remember('module_descriptions', 60*60*24*365, function () use ($lang_id) {
            return DB::table('module_descriptions')->where('lang_id', $lang_id)->first();
        });
        if(!$module_description)
            $module_description = DB::table('module_descriptions')->where('lang_id', 1)->first();

        $modules =  Cache::remember('modules', 60*60*24*365, function () {
            return DB::table('modules')->orderBy('order', 'asc')->get();
        });

        $features =  Cache::remember('features', 60*60*24*365, function () {
            return DB::table('features')->orderBy('order', 'asc')->get();
        });

        $testimonials =  Cache::remember('testimonials', 60*60*24*365, function () {
            return DB::table('testimonials')->orderBy('order', 'asc')->get();
        });

        $socials =  Cache::remember('socials', 60*60*24*365, function () {
            return DB::table('socials')->orderBy('order', 'asc')->get();
        });

        $blogs =  Cache::remember('blogs', 60*60*24*30, function () {
            return DB::table('blogs')->latest()->take(3)->get();
        });

        $pages =  Cache::remember('pages', 60*60*24*30, function () {
            return DB::table('pages')->get();
        });

        $languages =  Cache::remember('languages', 60*60*24*30, function () {
            return DB::table('languages')->where('is_active', true)->get();
        });

        $coupon_list = Coupon::where([
            ['is_active', true],
            ['expired_date', '>=', date("Y-m-d")]
        ])->get();

        $all_features = $this->features();

        if($general_setting->frontend_layout == 'regular') {
            return view('landlord.index', compact('general_setting', 'hero', 'all_features', 'packages', 'faq_description', 'faqs', 'modules', 'module_description', 'features', 'testimonials', 'socials','blogs', 'pages', 'languages', 'tenant_signup_description', 'present_lang', 'coupon_list'));

        }
        elseif($general_setting->frontend_layout == 'custom') {
            return view('landlord.custom_index', compact('general_setting','hero', 'all_features', 'packages', 'modules', 'module_description', 'testimonials', 'tenant_signup_description'));
        }
    }

    public function signUp(Request $request)
    {
        $search = 'Terms';
        $terms_and_condition_page = Page::select('slug')->where('title', 'LIKE', "%{$search}%")->first();
        return view('landlord.signup', compact('request', 'terms_and_condition_page'));
    }

    public function updateTenantDB()
    {
        if(!env('USER_VERIFIED'))
            return redirect()->back()->with('not_permitted', 'This feature is disable for demo!');
        $tenant_all = Tenant::all();

        if(count($tenant_all)) {
            \Artisan::call('tenants:migrate');
            \Artisan::call('tenants:seed');
            return redirect()->back()->with('message', 'All tenant DB updated successfully!');
        }
        else
            return redirect()->back()->with('message', 'No domain exist!');
    }

    public function updateSuperadminDB()
    {
        if(!env('USER_VERIFIED'))
            return redirect()->back()->with('not_permitted', 'This feature is disable for demo!');
        \Artisan::call('migrate --path=/database/migrations/landlord');
        \Artisan::call('db:seed');
        return redirect()->back()->with('message', 'SuperAdmin DB updated success!');
    }

    public function backupTenantDB()
    {
        if(!env('USER_VERIFIED'))
            return redirect()->back()->with('not_permitted', 'This feature is disable for demo!');
        $tenants = Tenant::select('id')->get();
        if (count($tenants)) {
            // Database configuration
            $host = env('DB_HOST');
            $username = env('DB_USERNAME');
            $password = env('DB_PASSWORD');
            $zip = new ZipArchive();
            $zipFileName = 'db_backup_' . date("Ymdhis") . '.zip';
            $zip->open(public_path() . '/' . $zipFileName, ZipArchive::CREATE);
            foreach ($tenants as $key => $tenant) {
                $database_name = env('DB_PREFIX').$tenant->id;
                // Get connection object and set the charset
                $conn = mysqli_connect($host, $username, $password, $database_name);
                $conn->set_charset("utf8");
                // Get All Table Names From the Database
                $tables = array();
                $sql = "SHOW TABLES";
                $result = mysqli_query($conn, $sql);
                $tables = [];
                while ($row = mysqli_fetch_row($result)) {
                    $tables[] = $row[0];
                }
                $sqlScript = "";
                foreach ($tables as $table) {
                    // Prepare SQLscript for creating table structure
                    $query = "SHOW CREATE TABLE $table";
                    $result = mysqli_query($conn, $query);
                    $row = mysqli_fetch_row($result);

                    $sqlScript .= "\n\n" . $row[1] . ";\n\n";

                    $query = "SELECT * FROM $table";
                    $result = mysqli_query($conn, $query);

                    $columnCount = mysqli_num_fields($result);
                    // Prepare SQLscript for dumping data for each table
                    for ($i = 0; $i < $columnCount; $i ++) {
                        while ($row = mysqli_fetch_row($result)) {
                            $sqlScript .= "INSERT INTO $table VALUES(";
                            for ($j = 0; $j < $columnCount; $j ++) {
                                $row[$j] = $row[$j];

                                if (isset($row[$j])) {
                                    $sqlScript .= '"' . $row[$j] . '"';
                                } else {
                                    $sqlScript .= '""';
                                }
                                if ($j < ($columnCount - 1)) {
                                    $sqlScript .= ',';
                                }
                            }
                            $sqlScript .= ");\n";
                        }
                    }

                    $sqlScript .= "\n";
                }
                if(!empty($sqlScript))
                {
                    // Save the SQL script to a backup file
                    $sqlFileName = $database_name . '_backup_' . date("Ymdhis") . '.sql';
                    $backup_file_path = public_path().'/dbBackup/' . $sqlFileName;
                    $fileHandler = fopen($backup_file_path, 'w+');
                    $number_of_lines = fwrite($fileHandler, $sqlScript);
                    fclose($fileHandler);
                    //file added to the zip
                    $zip->addFile($backup_file_path, $sqlFileName);
                }
            }
            $zip->close();
            $files = glob(public_path().'/dbBackup/*'); // get all file names
            foreach($files as $file){ // iterate files
            if(is_file($file)) {
                unlink($file); // delete file
            }
            }
            return redirect($zipFileName);
        }
        else {
            return redirect()->back()->with('message', 'No database to backup');
        }
    }

    public function contactForRenewal(Request $request)
    {
        $subdomain = $request->id;
        $general_setting = DB::table('general_settings')->select('meta_title', 'meta_description', 'site_logo', 'phone', 'email', 'developed_by')->latest()->first();
        $payment_gateway_count = DB::table('external_services')->where('active', 1)->count();
        $packages = Package::where('is_active', true)->get();
        $coupon_list = Coupon::where([
            ['is_active', true],
            ['expired_date', '>=', date("Y-m-d")]
        ])->get();
        $socials =  Cache::remember('socials', 60*60*24*365, function () {
            return DB::table('socials')->get();
        });
        return view('landlord.renewal', compact('subdomain', 'general_setting', 'payment_gateway_count', 'packages', 'socials', 'coupon_list'));
    }

    public function renewSubscription(Request $request)
    {
        if(!env('USER_VERIFIED'))
            return redirect()->back()->with('not_permitted', 'This feature is disable for demo!');
        //return $request;
        $tenant = Tenant::find($request->id);
        if($tenant) {
            $packageData = Package::select('monthly_fee', 'yearly_fee', 'permission_id', 'features')->find($request->package_id);

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

            $prevPackageData = Package::select('permission_id')->find($tenant->package_id);
            if (!empty($prevPackageData->permission_id)) {
                $prev_permission_ids = explode(",", $prevPackageData->permission_id);
            }

            //collecting permission ids which needs to be deleted
            foreach ($prev_permission_ids as $key => $prev_permission_id) {
                if(!in_array($prev_permission_id, $permission_ids)) {
                    $abandoned_permission_ids[] = $prev_permission_id;
                }
            }

            if($request->subscription_type == 'monthly') {
                //$request->price = $packageData->monthly_fee;
                $request->numberOfDaysToExpired = 30;
            }
            elseif($request->subscription_type == 'yearly') {
                //$request->price = $packageData->yearly_fee;
                $request->numberOfDaysToExpired = 365;
            }

            $request->modules = $modules;
            $request->permission_ids = json_encode($permission_ids);
            $request->abandoned_permission_ids = json_encode($abandoned_permission_ids);
            $request->renewal = 1;
            $request->email = $tenant->email;
            $payment_gateways = DB::table('external_services')->where('type', 'payment')->where('active', true)->get();
            $search = 'Terms';
            $terms_and_condition_page = Page::select('slug')->where('title', 'LIKE', "%{$search}%")->first();
            return view('payment.tenant_checkout', compact('request', 'payment_gateways', 'terms_and_condition_page'));
        }
        else
            return redirect()->back()->with('message', 'This subdomain does not exist!');
    }
}
