<?php

namespace App\Imports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use App\Helpers\CustomValidator;
use App\Helpers\DataHelper;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class UsersImport implements ToArray, WithHeadingRow
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
        $passwordHash = Hash::make('admin123');
        foreach ($rows as $row) {

            $validatorRule = [
                'job_title' => 'required|string',
                'email_address' => 'required|email|unique:users,email',
                'firstname_lastname' => 'required|string',
                'registered_since' => 'required|string',
                'phone' => 'required|string'
            ];

            $validate_result = CustomValidator::validator($row, $validatorRule);

            if($validate_result['code']!== 200){
                \Log::info(json_encode($validate_result).' For Row '.json_encode($row));
                self::$failureCount++;
                continue;
            }

            //Create first and last name
            $nameComponent = DataHelper::createFirstLastName($row['firstname_lastname']);
            if(sizeof($nameComponent) != 2){
                \Log::info('First name or last name not found for record :'.json_encode($row));
                self::$failureCount++;
                continue;
            }

            $userData = [];
            $userData['first_name'] = $nameComponent[0];
            $userData['last_name'] = $nameComponent[0];
            $userData['email'] = $row['email_address'];
            $userData['role_id'] = config('constants.roles.customer'); //Give user role by default
            $userData['phone'] = $row['phone'];
            if(!empty($row['job_title'])){
                $userData['job_title'] = $row['job_title'];
            }
            $userData['password'] = $passwordHash; //Setting a default password
            $userData['registered_since'] = Carbon::parse($row['registered_since']);
            $userData['email_verified_at'] = Carbon::now(); //Setting all email as verified
            $userData['updated_at'] = Carbon::now(); //Setting as current date
            $userData['created_at'] = Carbon::now(); //Setting as current date

            $insertData[] = $userData;
            self::$successCount++;

        }

        if(empty($insertData)){
            \Log::info('No record found to import');
            return;
        }

        \DB::beginTransaction();
        try {
            $insertData = collect($insertData);

            $chunks = $insertData->chunk(500);

            foreach ($chunks as $chunk)
            {
               $dataInsert = User::insert($chunk->toArray());
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
