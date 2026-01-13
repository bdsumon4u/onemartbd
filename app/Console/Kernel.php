<?php

namespace App\Console;

use App\Models\Order;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @return void
     */
    #[\Override]
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();
        $schedule->call(function (): void {
            /* fraud checker API */
            $un_checked_order = Order::select('id', 'status', 'customer_phone', 'customer_activity')->where([['status', 2], ['customer_activity', null]])->get();
            foreach ($un_checked_order as $item) {
                if (strlen((string) $item->customer_phone) == 11) {
                    $curl = curl_init();
                    curl_setopt_array($curl, [
                        CURLOPT_URL => 'https://courierrank.com/api/get-customer-details/'.$item->customer_phone,
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => '',
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 0,
                        CURLOPT_FOLLOWLOCATION => true,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => 'POST',
                        CURLOPT_HTTPHEADER => [
                            'Authorization: Bearer '.env('TJ_FC_API'),
                        ],
                    ]);
                    $response = curl_exec($curl);
                    // dd($response);
                    if (json_decode($response) && json_decode($response)->phone) {
                        $data = [
                            'total' => json_decode($response)->pathao_delivered + json_decode($response)->pathao_returned + json_decode($response)->steadfast_delivered + json_decode($response)->steadfast_returned + json_decode($response)->redx_delivered + json_decode($response)->redx_returned,
                            'total_delivered' => json_decode($response)->pathao_delivered + json_decode($response)->steadfast_delivered + json_decode($response)->redx_delivered,
                            'total_returned' => json_decode($response)->pathao_returned + json_decode($response)->steadfast_returned + json_decode($response)->redx_returned,
                            'pathao_delivered' => json_decode($response)->pathao_delivered,
                            'pathao_returned' => json_decode($response)->pathao_returned,
                            'steadfast_delivered' => json_decode($response)->steadfast_delivered,
                            'steadfast_returned' => json_decode($response)->steadfast_returned,
                            'redx_delivered' => json_decode($response)->redx_delivered,
                            'redx_returned' => json_decode($response)->redx_returned,
                        ];

                        $item->update([
                            'customer_activity' => json_encode($data),
                        ]);
                    }

                    /*$curl = curl_init();

                    curl_setopt_array($curl, array(
                        CURLOPT_URL => 'https://bdcourier.com/api/pro/courier-check?phone='. $item->customer_phone,
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => '',
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 0,
                        CURLOPT_FOLLOWLOCATION => true,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => 'POST',
                        CURLOPT_HTTPHEADER => array(
                            'Authorization: Bearer ' . env('TJ_FC_API'),
                        ),
                    ));

                    $response = curl_exec($curl);

                    curl_close($curl);


                    if (json_decode($response) && json_decode($response)->status == 'success') {
                        $data = [
                            'total' => json_decode($response)->courierData->summary->total_parcel,
                            'total_delivered' => json_decode($response)->courierData->summary->success_parcel,
                            'total_returned' => json_decode($response)->courierData->summary->cancelled_parcel,
                            'pathao_delivered' => json_decode($response)->courierData->pathao->success_parcel,
                            'pathao_returned' => json_decode($response)->courierData->pathao->cancelled_parcel,
                            'steadfast_delivered' => json_decode($response)->courierData->steadfast->success_parcel,
                            'steadfast_returned' => json_decode($response)->courierData->steadfast->cancelled_parcel,
                            'redx_delivered' => json_decode($response)->courierData->redx->success_parcel,
                            'redx_returned' => json_decode($response)->courierData->redx->cancelled_parcel,
                        ];

                        //dd($data);
                        $item->update([
                            'customer_activity' => json_encode($data),
                        ]);
                    }*/

                }
            }

            /* pathao city and zone fetch */

        })->everyMinute();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    #[\Override]
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
