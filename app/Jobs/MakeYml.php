<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

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

    private $products;
    private $token;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($products, $token)
    {
        $this->products = $products;
        $this->token = $token;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
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

        $offers = [];
        foreach ($this->products as $product) {
            $offers[] = (new OfferSimple())
                ->setMarketCategory($product['category'])
                ->setName($product["name"])
                ->setPrice($product["price"])
                ->addPicture($product['image'])
            ;
        }

        (new Generator($settings))->generate(
            $shopInfo,
            $currencies,
            $categories,
            $offers,
            $deliveries
        );

        Storage::put('ymls/' . $this->token . '_parsed.yml', file_get_contents($file));
    }
}
