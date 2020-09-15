<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use App\Models\QueueState;

use Bukashk0zzz\YmlGenerator\Model\Offer\OfferSimple;
use Bukashk0zzz\YmlGenerator\Model\Category;
use Bukashk0zzz\YmlGenerator\Model\Currency;
use Bukashk0zzz\YmlGenerator\Model\Delivery;
use Bukashk0zzz\YmlGenerator\Model\ShopInfo;
use Bukashk0zzz\YmlGenerator\Settings;
use Bukashk0zzz\YmlGenerator\Generator;

class MakeYml implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $queueState;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($queueStateId)
    {
        $this->queueState = QueueState::where("id", $queueStateId)->first();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $this->queueState->mode = 1;
            $this->queueState->save();
            $file = tempnam(sys_get_temp_dir(), 'YMLGenerator');
            $settings = (new Settings())
                ->setOutputFile($file)
                ->setEncoding('UTF-8')
            ;

            $shopInfo = (new ShopInfo())
                // ->setName('BestShop')
                // ->setCompany('Best online seller Inc.')
                // ->setUrl('http://www.best.seller.com/')
            ;

            $currencies = [];
            $currencies[] = (new Currency())
                // ->setId('USD')
                // ->setRate(1)
            ;

            $categories = [];
            $categories[] = (new Category())
                // ->setId(1)
                // ->setName($this->faker->name)
            ;

            $deliveries = [];
            $deliveries[] = (new Delivery())
                // ->setCost(2)
                // ->setDays(1)
                // ->setOrderBefore(14)
            ;

            $products = json_decode($this->queueState->data);
            $offers = [];
            foreach ($products as $product) {
                $offers[] = (new OfferSimple())
                    ->setMarketCategory($product->category)
                    ->setName($product->name)
                    ->setPrice($product->price)
                    ->addPicture($product->image)
                ;
            }

            (new Generator($settings))->generate(
                $shopInfo,
                $currencies,
                $categories,
                $offers,
                $deliveries
            );

            Storage::put('ymls/' . $this->queueState->file_id . '_parsed.yml', file_get_contents($file));
            $this->queueState->mode = 2;
            $this->queueState->save();
        } catch (\Exception $e) {
            $this->queueState->mode = -1;
            $this->queueState->save();
        }
    }
}
