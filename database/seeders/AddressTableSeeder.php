<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Address;
use App\Enums\AddressType;


class AddressTableSeeder extends Seeder
{
    public array $addresses = [
        [
            'label'        => AddressType::HOME,
            'label_name'   => "Home",
            'address'      => "Block 5, Gulshan Town, Karachi 75500, Pakistan",
            'apartment'    => "apartment 5/6",
            'latitude'     => "24.8607",
            'longitude'    => "67.0011",
            'user_id'      => "1",
            'creator_type' => "",
            'creator_id'   => "0",
            'editor_type'  => "",
            'editor_id'    => "0",
        ],
        [
            'label'        => AddressType::WORK,
            'label_name'   => "Work",
            'address'      => "Street 17, DHA Phase 5, Karachi 75500, Pakistan",
            'apartment'    => "apartment 5/6",
            'latitude'     => "24.8607",
            'longitude'    => "67.0011",
            'user_id'      => "1",
            'creator_type' => "",
            'creator_id'   => "0",
            'editor_type'  => "",
            'editor_id'    => "0",
        ] ,


        [
            'label'        => AddressType::HOME,
            'label_name'   => "Home",
            'address'      => "Block 5, Gulshan Town, Karachi 75500, Pakistan",
            'apartment'    => "apartment 5/6",
            'latitude'     => "24.8607",
            'longitude'    => "67.0011",
            'user_id'      => "2",
            'creator_type' => "",
            'creator_id'   => "0",
            'editor_type'  => "",
            'editor_id'    => "0",
        ],
        [
            'label'        => AddressType::WORK,
            'label_name'   => "Work",
            'address'      => "Street 17, DHA Phase 5, Karachi 75500, Pakistan",
            'apartment'    => "apartment 5/6",
            'latitude'     => "24.8607",
            'longitude'    => "67.0011",
            'user_id'      => "2",
            'creator_type' => "",
            'creator_id'   => "0",
            'editor_type'  => "",
            'editor_id'    => "0",
        ] ,

        [
            'label'        => AddressType::HOME,
            'label_name'   => "Home",
            'address'      => "Block 5, Gulshan Town, Karachi 75500, Pakistan",
            'apartment'    => "apartment 5/6",
            'latitude'     => "24.8607",
            'longitude'    => "67.0011",
            'user_id'      => "3",
            'creator_type' => "",
            'creator_id'   => "0",
            'editor_type'  => "",
            'editor_id'    => "0",
        ],
        [
            'label'        => AddressType::WORK,
            'label_name'   => "Work",
            'address'      => "Street 17, DHA Phase 5, Karachi 75500, Pakistan",
            'apartment'    => "apartment 5/6",
            'latitude'     => "24.8607",
            'longitude'    => "67.0011",
            'user_id'      => "3",
            'creator_type' => "",
            'creator_id'   => "0",
            'editor_type'  => "",
            'editor_id'    => "0",
        ] ,


        [
            'label'        => AddressType::HOME,
            'label_name'   => "Home",
            'address'      => "Block 5, Gulshan Town, Karachi 75500, Pakistan",
            'apartment'    => "apartment 5/6",
            'latitude'     => "24.8607",
            'longitude'    => "67.0011",
            'user_id'      => "4",
            'creator_type' => "",
            'creator_id'   => "0",
            'editor_type'  => "",
            'editor_id'    => "0",
        ],
        [
            'label'        => AddressType::WORK,
            'label_name'   => "Work",
            'address'      => "Street 17, DHA Phase 5, Karachi 75500, Pakistan",
            'apartment'    => "apartment 5/6",
            'latitude'     => "24.8607",
            'longitude'    => "67.0011",
            'user_id'      => "4",
            'creator_type' => "",
            'creator_id'   => "0",
            'editor_type'  => "",
            'editor_id'    => "0",
        ] ,

        [
            'label'        => AddressType::HOME,
            'label_name'   => "Home",
            'address'      => "Block 5, Gulshan Town, Karachi 75500, Pakistan",
            'apartment'    => "apartment 5/6",
            'latitude'     => "24.8607",
            'longitude'    => "67.0011",
            'user_id'      => "5",
            'creator_type' => "",
            'creator_id'   => "0",
            'editor_type'  => "",
            'editor_id'    => "0",
        ],
        [
            'label'        => AddressType::WORK,
            'label_name'   => "Work",
            'address'      => "Street 17, DHA Phase 5, Karachi 75500, Pakistan",
            'apartment'    => "apartment 5/6",
            'latitude'     => "24.8607",
            'longitude'    => "67.0011",
            'user_id'      => "5",
            'creator_type' => "",
            'creator_id'   => "0",
            'editor_type'  => "",
            'editor_id'    => "0",
        ] ,
    ];


    public function run(){
        if (env('DEMO_MODE')) {
            foreach ($this->addresses as $address) {
                Address::create([
                    'label'        => $address['label'],
                    'label_name'   => $address['label_name'],
                    'address'      => $address['address'],
                    'apartment'    => $address['apartment'],
                    'latitude'     => $address['latitude'],
                    'longitude'    => $address['longitude'],
                    'user_id'      => $address['user_id'],
                    'creator_type' => $address['creator_type'],
                    'creator_id'   => $address['creator_id'],
                    'editor_type'  => $address['editor_type'],
                    'editor_id'    => $address['editor_id'],
                ]);
            }
        }
    }
}
