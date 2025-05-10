<!DOCTYPE html>
<html>
    <head>
        <link rel="icon" type="image/png" href="{{url('logo', $general_setting->site_logo)}}" />
        <title>{{$lims_sale_data->customer->name.'_Sale_'.$lims_sale_data->reference_no}}</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
        <style type="text/css">
            span,td {
                font-size: 16px;
                line-height:1.5;
            }
            @media print {
                .hidden-print {
                    display: none !important;
                }
                /*tr.table-header {*/
                /*    background-color:rgb(1, 75, 148) !important;*/
                /*    -webkit-print-color-adjust: exact;*/
                /*}*/
                /*td.td-text {*/
                /*    background-color:rgb(205, 218, 235) !important;*/
                /*    -webkit-print-color-adjust: exact;*/
                /*}*/
            }
            table,tr,td {font-family: sans-serif;border-collapse: collapse;}
        </style>
        
        <script>
            function printThisWindow(){
                window.print();
                window.close();
            }
            function closeThisWindow(){
                window.close();
            }
        </script>
    </head>
    <body class="p-2">
        @if(preg_match('~[0-9]~', url()->previous()))
        @php $url = '../../pos'; @endphp
        @else
            @php $url = url()->previous(); @endphp
        @endif
        <div class="hidden-print">
            <!--<table>-->
            <!--    <tr>-->
            <!--        <td>-->
                        <!--<a href="{{$url}}" class="btn btn-info"><i class="fa fa-arrow-left"></i> {{trans('file.Back')}}</a> -->
            <!--        </td>-->
            <!--        <td>-->
            <!--            <button onclick="printThisWindow();" class="btn btn-primary"><i class="dripicons-print"></i> {{trans('file.Print')}}</button>-->
                        
            <!--            <button onclick="closeThisWindow();" class="btn btn-danger"><i class="dripicons-print"></i> {{trans('file.Close')}}</button>-->
            <!--        </td>-->
            <!--    </tr>-->
            <!--</table>-->
            
            <div class="text-center border-bottom pb-1">
                <button onclick="printThisWindow();" class="btn btn-secondary px-3"><i class="dripicons-print"></i> {{trans('file.Print')}}</button>
            @if(Auth::user())
                <button onclick="closeThisWindow();" class="btn btn-danger px-3"><i class="dripicons-print"></i> {{trans('file.Close')}}</button>
            @endif
            </div>
            <br>
        </div>
        <!--<table style="width: 100%;border-collapse: collapse;">-->
        <!--    <tr>-->
        <!--        <td colspan="2" style="padding:9px 0;width:40%">-->
        <!--            <h1 style="margin:0">{{$lims_biller_data->company_name}}</h1>-->
        <!--            <div>-->
        <!--                <span>Address:</span>&nbsp;&nbsp;<span>{{$lims_warehouse_data->address}}</span>-->
        <!--            </div>-->
        <!--            <div>-->
        <!--                <span>Phone:</span>&nbsp;&nbsp;<span>{{$lims_warehouse_data->phone}}</span>-->
        <!--            </div>-->
        <!--            @if($general_setting->vat_registration_number)-->
        <!--            <div>-->
        <!--                <span>{{trans('file.VAT Number')}}:</span>&nbsp;&nbsp;<span>{{$general_setting->vat_registration_number}}</span>-->
        <!--            </div>-->
        <!--            @endif-->
                   
        <!--        </td>-->
        <!--        <td style="width:30%; text-align: middle; vertical-align: top;">-->
        <!--            <img src="{{url('logo', $general_setting->site_logo)}}" height="80" width="120">-->
        <!--        </td>-->
        <!--        <td style="padding:5px -19px;width:30%;text-align:right;">-->
        <!--            <div style="display: flex;justify-content: space-between;border-bottom:1px solid #aaa">-->
        <!--                <span>Invoice No:</span> <span>{{$lims_sale_data->reference_no}}</span>-->
        <!--            </div>-->
        <!--            <div style="display: flex;justify-content: space-between;border-bottom:1px solid #aaa">-->
        <!--                <span>Date:</span> <span>{{$lims_sale_data->created_at}}</span>-->
        <!--            </div>-->
        <!--            @if($paid_by_info)-->
        <!--                <div style="display: flex;justify-content: space-between;border-bottom:1px solid #aaa">-->
        <!--                    <span>Paid By:</span> <span>{{$paid_by_info}}</span>-->
        <!--                </div>-->
        <!--            @endif-->
        <!--        </td>-->
        <!--    </tr>-->
        <!--</table>-->
        
        <div class="px-2 mb-2">
            <img src="{{url('images/biller', $lims_biller_data->image)}}" style="width:100%;">
        </div>
         
        <div class="p-2 bg-light mb-3" style="border:1px solid black;">
            <div class="row">
                <div class="col-5">
                    <div class="fw-bold">
                        <span>Date:</span> <span>{{$lims_sale_data->created_at}}</span>
                    </div>
                    <div class="fw-bold">
                        <span>Invoice No:</span> <span>{{$lims_sale_data->reference_no}}</span>
                    </div>
                    <!--<div class="fw-bold">-->
                    <!--    <span>Paid By:</span> <span>{{$paid_by_info}}</span>-->
                    <!--</div>-->
                    <div class="fw-bold">
                        <span>Sale Status:</span> <span>
                        @if($lims_sale_data->sale_status==1)
                            {{trans('file.Completed')}}
                        @elseif($lims_sale_data->sale_status==2)
                            {{trans('file.Completed')}}
                        @endif
                        </span>
                    </div>
                    <div class="fw-bold">
                        <span>Payment Status:</span> <span>
                        @if($lims_sale_data->payment_status==1)
                            {{trans('file.Pending')}}
                        @elseif($lims_sale_data->payment_status==2)
                            {{trans('file.Due')}}
                        @elseif($lims_sale_data->payment_status==3)
                            {{trans('file.Partial')}}
                        @elseif($lims_sale_data->payment_status==4)
                            {{trans('file.Paid')}}
                        @endif
                        
                        </span>
                    </div>
                </div>
                <div class="col-5">
                    <div class="fw-bold">
                        <span>Invoice To:</span> <span>{{$lims_customer_data->name}}</span>
                    </div>
                    <div>
                        <span>Address:</span> <span>{{$lims_customer_data->address}}</span>
                    </div>
                    <div>
                        <span>{{$lims_customer_data->city}}
                        @if($lims_customer_data->state)
                        , {{$lims_customer_data->state}}
                        @endif
                        @if($lims_customer_data->postal_code)
                         - {{$lims_customer_data->postal_code}}
                        @endif 
                        </span>
                    </div>
                    <div>
                        <span>Phone:</span> <span>{{$lims_customer_data->phone_number}}</span>
                    </div>
                </div>
                <div class="col-2 ps-0 text-end">
                    <?php echo '<img class="m-2" style="width:80px;" src="data:image/png;base64,' . DNS2D::getBarcodePNG($qrText, 'QRCODE') . '" alt="barcode"   />';?>
                </div>
            </div>
        </div>
        
        <!--<table style="width: 100%;border-collapse: collapse; margin-top: 4px;">-->
        <!--    <tr>-->
        <!--        <td colspan="3" style="padding:4px 0;width:30%;vertical-align:top">-->
        <!--            <h2 style="padding:3px 10px; margin-bottom:0">Bill To</h2>-->
        <!--            <div style="margin-top: 10px;margin-left: 10px">-->
        <!--                <span>{{$lims_customer_data->name}}</span>-->
        <!--            </div>-->
        <!--            <div style="margin-left: 10px">-->
        <!--                <span>VAT Number:</span>&nbsp;&nbsp;<span>{{$lims_customer_data->tax_no}}</span>-->
        <!--            </div>-->
        <!--            <div style="margin-left: 10px">-->

        <!--                <span>Address:</span>&nbsp;&nbsp;-->
        <!--                @if($lims_sale_data->sale_type == 'online')-->
        <!--                <span>{{$lims_sale_data->shipping_name}}, {{$lims_sale_data->shipping_address}}, {{$lims_sale_data->shipping_city}}, {{$lims_sale_data->shipping_country}}, {{$lims_sale_data->shipping_zip}}</span>-->
        <!--                @else-->
        <!--                <span>{{$lims_customer_data->address}}</span>-->
        <!--                @endif-->
        <!--            </div>-->
        <!--            @if(isset($lims_customer_data->phone_number) || isset($lims_sale_data->shipping_phone))-->
        <!--            <div style="margin-bottom: 10px;margin-left: 10px">-->
        <!--                <span>Phone:</span>&nbsp;&nbsp;-->
        <!--                @if($lims_sale_data->sale_type == 'online')-->
        <!--                <span>{{$lims_sale_data->shipping_phone}}-->
        <!--                @else-->
        <!--                <span>{{$lims_customer_data->phone_number}}</span>-->
        <!--                @endif-->
        <!--            </div>-->
        <!--            @endif-->
        <!--        </td>-->
        <!--        <td colspan="4" style="width:60%">-->

        <!--        </td>-->
        <!--    </tr>-->
        <!--</table>-->
        
        
        <table dir="@if( Config::get('app.locale') == 'ar' || $general_setting->is_rtl){{'rtl'}}@endif" style="width: 100%;border-collapse: collapse;">
            <tr class="table-header fw-bold">
                <td style="border:1px solid #222;padding:1px 3px;width:4%;text-align:center">#</td>
                <td style="border:1px solid #222;padding:1px 3px;width:39%;text-align:center">{{trans('file.Description')}}</td>
                <td style="border:1px solid #222;padding:1px 3px;width:12%;text-align:center">{{trans('file.Qty')}}</td>
                <td style="border:1px solid #222;padding:1px 3px;width:12%;text-align:center">{{trans('file.Unit Price')}}</td>
                
                <!--<td style="border:1px solid #222;padding:1px 3px;width:7%;text-align:center">{{trans('file.Total')}}</td>-->
                <!--<td style="border:1px solid #222;padding:1px 3px;width:7%;text-align:center">{{trans('file.Tax')}}</td>-->
                
                <td style="border:1px solid #222;padding:1px 2px;width:14%;text-align:center;">{{trans('file.Subtotal')}}</td>
            </tr>
            <?php
                $total_product_tax = 0;
                $totalPrice = 0;
            ?>
            
            @foreach($lims_product_sale_data as $key => $product_sale_data)
            <?php
                $lims_product_data = \App\Models\Product::find($product_sale_data->product_id);
                if($product_sale_data->sale_unit_id) {
                    $unit = \App\Models\Unit::select('unit_code')->find($product_sale_data->sale_unit_id);
                    $unit_code = $unit->unit_code;
                }
                else
                    $unit_code = '';

                if($product_sale_data->variant_id) {
                    $variant = \App\Models\Variant::select('name')->find($product_sale_data->variant_id);
                    $variant_name = $variant->name;
                }
                else
                    $variant_name = '';
                $totalPrice += $product_sale_data->net_unit_price * $product_sale_data->qty;

                $topping_names = [];
                $topping_prices = [];
                $topping_price_sum = 0;
        
                if ($product_sale_data->topping_id) {
                    $decoded_topping_id = json_decode(json_decode($product_sale_data->topping_id), true);
                    //dd(json_decode($product_sale_data->topping_id));
                    if (is_array($decoded_topping_id)) {
                        foreach ($decoded_topping_id as $topping) {
                            $topping_names[] = $topping['name']; // Extract name
                            $topping_prices[] = $topping['price']; // Extract price
                            $topping_price_sum += $topping['price']; // Sum up prices
                        }
                    }
                }
        
                $net_price_with_toppings = $product_sale_data->net_unit_price + $topping_price_sum;
                $total = ($product_sale_data->net_unit_price + $topping_price_sum) * $product_sale_data->qty;

                $subtotal = ($product_sale_data->total+ $topping_price_sum);
            ?>
            <tr>
                <td style="@if( Config::get('app.locale') == 'ar' || $general_setting->is_rtl){{'border-right:1px solid #222;'}}@endif border:1px solid #222;padding:1px 3px;text-align: center;">{{$key+1}}</td>
                <td style="border:1px solid #222;padding:1px 3px;font-size: 15px;line-height: 1.2;">

                    <!--<span style="font-weight: bold;">Product Name</span>: -->

                    {!!$lims_product_data->name!!}

                    @if(!empty($topping_names))
                        <br><small>({{ implode(', ', $topping_names) }})</small>
                    @endif

                    @foreach($product_custom_fields as $index => $fieldName)
                        <?php $field_name = str_replace(" ", "_", strtolower($fieldName)) ?>
                        @if($lims_product_data->$field_name)
                            @if(!$index)
                            <br>
                            <span style="font-weight: bold;">{{ $fieldName }}</span>
                            {{ ': ' . $lims_product_data->$field_name }}
                            @else
                            <br>
                            <span style="font-weight: bold;">{{ $fieldName }}</span>
                            {{': ' . $lims_product_data->$field_name }}
                            @endif
                        @endif
                    @endforeach
                    @if($product_sale_data->imei_number && !str_contains($product_sale_data->imei_number, "null") )
                    <br>IMEI or Serial: {{$product_sale_data->imei_number}}
                    @endif
                    <!-- warranty -->
                     @if (isset($product_sale_data->warranty_duration))
                            <br>
                            <span style="font-weight: bold;">Warranty</span>{{ ': ' . $product_sale_data->warranty_duration }}
                            <br>
                            <span style="font-weight: bold;">Will Expire</span>{{ ': ' . $product_sale_data->warranty_end }}
                     @endif
                     <!-- guarantee -->
                     @if (isset($product_sale_data->guarantee_duration))
                            <br>
                            <span style="font-weight: bold;">Guarantee</span>{{ ': ' . $product_sale_data->guarantee_duration }}
                            <br>
                            <span style="font-weight: bold;">Will Expire</span>{{ ': ' . $product_sale_data->guarantee_end }}
                     @endif
                </td>
                <td style="border:1px solid #222;padding:1px 3px;text-align:center">{{$product_sale_data->qty.' '.$unit_code.' '.$variant_name}}</td>
                <td style="border:1px solid #222;padding:1px 3px;text-align:center">{{number_format($product_sale_data->net_unit_price, $general_setting->decimal, '.', ',')}}
                @if(!empty($topping_prices))
                    <br><small>+ {{ implode(' + ', array_map(fn($price) => number_format($price, $general_setting->decimal, '.', ','), $topping_prices)) }}</small>
                @endif
                </td>
                <!--<td style="border:1px solid #222;padding:1px 3px;text-align:center">{{ number_format($total, $general_setting->decimal, '.', ',') }}</td>-->
                <!--<td style="border:1px solid #222;padding:1px 3px;text-align:center">{{number_format($product_sale_data->tax, $general_setting->decimal, '.', ',')}}</td>-->
                <td style="border:1px solid #222;border-right:1px solid #222;padding:1px 3px;text-align:center;font-size: 15px;">
                    <!--{{number_format($subtotal, $general_setting->decimal, '.', ',')}}-->
                    {{number_format($product_sale_data->qty*$product_sale_data->net_unit_price, $general_setting->decimal, '.', ',')}}
                
                </td>
            </tr>
            @endforeach
            <tr>
                <td colspan="2" rowspan="@if($general_setting->invoice_format == 'gst' && $general_setting->state == 2) 8 @else 7 @endif">
                    
                </td>
                <td colspan="2" class="td-text" style="border:1px solid #222;padding:1px 3px;">
                    {{trans('file.Total')}}
                </td>
                <td class="td-text" style="border:1px solid #222;padding:1px 3px;text-align: center;font-size: 15px;">
                        {{number_format((float)($lims_sale_data->total_price - ($lims_sale_data->total_tax+$lims_sale_data->order_tax) ) ,$general_setting->decimal, '.', ',')}}
                </td>
            </tr>
            @if($general_setting->invoice_format == 'gst' && $general_setting->state == 1)
                <tr>
                    <td colspan="2" class="td-text" style="border:1px solid #222;padding:1px 3px;">
                        IGST
                    </td>
                    <td class="td-text" style="border:1px solid #222;padding:1px 3px;text-align: center;font-size: 15px;">
                        {{number_format((float)($lims_sale_data->total_tax+$lims_sale_data->order_tax) ,$general_setting->decimal, '.', ',')}}
                    </td>
                </tr>
            @elseif($general_setting->invoice_format == 'gst' && $general_setting->state == 2)
                <tr>
                    <td colspan="2" class="td-text" style="border:1px solid #222;padding:1px 3px;">
                        SGST
                    </td>
                    <td class="td-text" style="border:1px solid #222;padding:1px 3px;text-align: center;font-size: 15px;">
                        {{number_format( ($lims_sale_data->total_tax+$lims_sale_data->order_tax) / 2 , $general_setting->decimal, '.', ',')}}
                    </td>
                </tr>
                <tr>
                    <td colspan="2" class="td-text" style="border:1px solid #222;padding:1px 3px;">
                        CGST
                    </td>
                    <td class="td-text" style="border:1px solid #222;padding:1px 3px;text-align: center;font-size: 15px;">
                        {{number_format( ($lims_sale_data->total_tax+$lims_sale_data->order_tax) / 2 , $general_setting->decimal, '.', ',')}}
                    </td>
                </tr>
            @else
                @if($lims_sale_data->total_tax+$lims_sale_data->order_tax)
                <tr>
                    <td colspan="2" class="td-text" style="border:1px solid #222;padding:1px 3px;">
                        {{trans('file.Tax')}}
                    </td>
                    <td class="td-text" style="border:1px solid #222;padding:1px 3px;text-align: center;font-size: 15px;">
                        {{number_format((float)($lims_sale_data->total_tax+$lims_sale_data->order_tax) ,$general_setting->decimal, '.', ',')}}
                    </td>
                </tr>
                @endif
            @endif
            @if($lims_sale_data->total_discount+$lims_sale_data->order_discount)
            <tr>
                <td colspan="2" class="td-text" style="border:1px solid #222;padding:1px 3px;">
                    {{trans('file.Discount')}}
                </td>
                <td class="td-text" style="border:1px solid #222;padding:1px 3px;text-align: center;font-size: 15px;">
                    {{number_format((float)($lims_sale_data->total_discount+$lims_sale_data->order_discount) ,$general_setting->decimal, '.', ',')}}
                </td>
            </tr>
            @endif
            
            <tr>
                <td colspan="2" class="td-text" style="border:1px solid #222;padding:1px 3px;">
                    {{trans('file.Previous Due')}}
                </td>
                <td class="td-text" style="border:1px solid #222;padding:1px 3px;text-align: center;font-size: 15px;">
                    <!--{{number_format((float)($lims_sale_data->grand_total - $lims_sale_data->paid_amount) ,$general_setting->decimal, '.', ',')}}-->
                    
                    {{number_format((float)($totalDue) ,$general_setting->decimal, '.', ',')}}
                </td>
            </tr>
            <tr>
                <td colspan="2" class="td-text" style="border:1px solid #222;padding:1px 3px;">{{trans('file.grand total')}}</td>
                <td class="td-text" style="border:1px solid #222;padding:1px 3px;text-align: center;font-size: 15px;">{{number_format((float)$totalDue+$lims_sale_data->grand_total ,$general_setting->decimal, '.', ',')}}</td>
            </tr>
            <!--<tr>-->
            <!--    @if($general_setting->currency_position == 'prefix')-->
            <!--        <td class="td-text" colspan="3" rowspan="4" style="border:1px solid #222;padding:1px 3px;text-align: center;vertical-align: bottom;font-size: 15px; vertical-align: top;">-->
            <!--            kkkk-->
            <!--        </td>-->
            <!--    @else-->
            <!--        <td class="td-text" colspan="3" rowspan="4" style="border:1px solid #222;padding:1px 3px;text-align: center;vertical-align: bottom;font-size: 15px; vertical-align: top;">-->
            <!--            {{trans('file.In Words')}}:<br><span style="text-transform:capitalize;font-size: 15px;">{{str_replace("-"," ",$numberInWords)}}</span> {{$currency_code}} only-->
            <!--        </td>-->
            <!--    @endif-->
            <!--</tr>-->
            <tr>
                <td colspan="2" class="td-text" style="border:1px solid #222;padding:1px 3px;">
                    {{trans('file.Paid')}}
                </td>
                <td class="td-text" style="border:1px solid #222;padding:1px 3px;text-align: center;font-size: 15px;">
                    {{number_format((float)$lims_sale_data->paid_amount ,$general_setting->decimal, '.', ',')}}
                </td>
            </tr>
            
            <tr>
                <td colspan="2" class="td-text" style="border:1px solid #222;padding:1px 3px;">
                    {{trans('file.Balance')}}
                </td>
                <td class="td-text" style="border:1px solid #222;padding:1px 3px;text-align: center;font-size: 15px;">
                    {{number_format(($totalDue+$lims_sale_data->grand_total)-$lims_sale_data->paid_amount ,$general_setting->decimal, '.', ',')}}
                </td>
            </tr>
        </table>
        
        
    
        <div class="fw-bold text-center my-2">
            <span>{{trans('file.In Words')}}: </span>
            {{$currency_code}} <span style="text-transform:capitalize;font-size: 15px;">{{str_replace("-"," ",$numberInWords)}}</span> only
        </div>
        
        <div class="my-3">
            @if($lims_sale_data->sale_note)
            <div>
                <span class="fw-bold">{{trans('file.Note')}}: </span>
                {{$lims_sale_data->sale_note}}
            </div>
            @endif
        </div>
        <!--<table style="width: 100%; border-collapse: collapse;margin-top:-9px;">
            <tr>
                <td style="width: 100%; text-align: center">
                    <br>
                    <?php //echo '<img style="max-width:100%" src="data:image/png;base64,' . DNS1D::getBarcodePNG($lims_sale_data->reference_no, 'C128') . '" alt="barcode"   />';?>
                    <br><br>
                    <?php //echo '<img style="width:5%" src="data:image/png;base64,' . DNS2D::getBarcodePNG($qrText, 'QRCODE') . '" alt="barcode"   />';?>
                </td>
            </tr>
        </table> -->
        
        <div class="mt-4 mx-2">
            <div class="row">
                <div class="col-7">
                    
                </div>
                <div class="col-5">
                    <div class="p-2 border">
                        <div class="text-start mb-5">
                            <span>Created by:</span> <span>{{$lims_biller_data->name}}</span>
                        </div>
                        <div class="text-start">
                            <span>Date:</span> <span>{{$lims_sale_data->created_at}}</span>
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>
        
        <div class="ms-2">
            <a class="link-secondary link-offset-2 link-underline link-underline-opacity-0 link-underline-opacity-100-hover" href="https://tradeaidbd.com/">www.tradeaidbd.com</a>
        </div>
        <script type="text/javascript">
            localStorage.clear();
            function auto_print() {
                window.print();

            }
            //setTimeout(auto_print, 1000);
        </script>
    </body>
</html>
