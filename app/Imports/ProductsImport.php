<?php

namespace App\Imports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use App\Helpers\CustomValidator;
use Carbon\Carbon;

class ProductsImport implements ToArray, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public static $successCount = 0;
    public static $failureCount = 0;

    public static function logCount()
    {
        \Log::info(self::$successCount.' Entries created successfully');
        \Log::info(self::$failureCount.' Entries were not created');
    }

    public function array(array $rows)
    {
        $insertData = [];
        foreach ($rows as $row) {

            $validatorRule = [
                'productname' => 'required|string|unique:products,name',
                'price' => 'required'
            ];

            $validate_result = CustomValidator::validator($row, $validatorRule);

            if($validate_result['code']!== 200){
                \Log::info(json_encode($validate_result).' For Row '.json_encode($row));
                self::$failureCount++;
                continue;
            }

            $productData = [];
            $productData['name'] = $row['productname'];
            $productData['price'] = $row['price'];
            $productData['updated_at'] = Carbon::now(); //Setting as current date
            $productData['created_at'] = Carbon::now(); //Setting as current date

            $insertData[] = $productData;
            self::$successCount++;
        }

        if(empty($insertData)){
            \Log::info('No record found to import');
            return;
        }

        \DB::beginTransaction();
        try {
            $insertData = collect($insertData);

            $chunks = $insertData->chunk(50);

            foreach ($chunks as $chunk)
            {
               $dataInsert = Product::insert($chunk->toArray());
            }

        } catch (\Exception $e) {
            \DB::rollBack();
            self::$failureCount = self::$failureCount+self::$successCount;
            self::$successCount = 0;
            \Log::info($e->getMessage());
            return;
        }
        \DB::commit();
        return;
    }
}
