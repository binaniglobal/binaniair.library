<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ProductionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $userSuper = [
            [
                'uuid' => 'c7a118aa-8096-479c-a300-b8b3c80933f0',
                'name' => 'Super-Admin',
                'surname' => 'BinaniAir',
                'email' => 'super-admin@binaniair.com',
                'phone' => '09000023456',
                'status' => 0,
                'email_verified_at' => null,
                'password' => Hash::make('BinaniAir2456@1234#*@'), // You might want to use a more secure default password
                'remember_token' => Str::random(60),
                'created_at' => '2024-05-24 07:34:24',
                'updated_at' => '2024-05-24 07:34:24',
            ],
        ];
        foreach ($userSuper as $userData) {
            $user = User::create($userData); // Assumes you are using the User model.
            $user->assignRole('super-admin');
//            if (!$user->hasPermissionTo(['view-manual'])) {
                $user->givePermissionTo(['view-manual']);
//            }
        }

        $userSuperAdmin = [
            [
                'uuid' => 'c7a118aa-8096-479c-a300-b8b3c20934ja',
                'name' => 'SuperAdmin',
                'surname' => 'BinaniAir',
                'email' => 'superadmin@binaniair.com',
                'phone' => '09000023457',
                'status' => 0,
                'email_verified_at' => null,
                'password' => Hash::make('BinaniAir2456@1234#*@'),
                'remember_token' => Str::random(60),
                'created_at' => '2024-05-24 07:34:24',
                'updated_at' => '2024-05-24 07:34:24',
            ]
        ];
        foreach ($userSuperAdmin as $userData) {
            $user = User::create($userData); // Assumes you are using the User model.
            $user->assignRole('SuperAdmin');
//            if (!$user->hasPermissionTo(['view-manual'])) {
                $user->givePermissionTo(['view-manual']);
//            }
        }

        $userAdmin = [
            [
                'uuid' => '8c4ec279-28fb-42ba-ad2b-3bc84d2ac3ba',
                'name' => 'Admin',
                'surname' => 'BinaniAir',
                'email' => 'admin@binaniair.com',
                'phone' => '09200023456',
                'status' => 0,
                'email_verified_at' => null,
                'password' => Hash::make('password'),
                'remember_token' => Str::random(60),
                'created_at' => '2024-05-24 07:34:24',
                'updated_at' => '2024-05-24 07:34:24',
            ],
            [
                'uuid' => '55a19813-6718-46b0-8ef0-d09da7a94b71',
                'name' => 'Hauwa',
                'surname' => 'Sanusi',
                'email' => 'hauwa.sanusi@binaniair.com',
                'phone' => '08032304168',
                'status' => 0,
                'email_verified_at' => null,
                'password' => Hash::make('password'),
                'remember_token' => Str::random(60),
                'created_at' => '2024-05-24 07:34:24',
                'updated_at' => '2025-04-10 12:06:31',
            ],
        ];
        foreach ($userAdmin as $userData) {
            $user = User::create($userData); // Assumes you are using the User model.
            $user->assignRole('admin');
//            if (!$user->hasPermissionTo(['view-manual'])) {
                $user->givePermissionTo(['view-manual']);
//            }
        }

        $users = [
            [
                'uuid' => '39dea29b-cad3-482e-9204-aaba3c4dafe3',
                'name' => 'User',
                'surname' => 'BinaniAir',
                'email' => 'user@binaniair.com',
                'phone' => '09100023456',
                'status' => 0,
                'email_verified_at' => null,
                'password' => Hash::make('password'),
                'remember_token' => Str::random(60),
                'created_at' => '2024-05-24 07:34:24',
                'updated_at' => '2024-05-24 07:34:24',
            ],
            [
                'uuid' => 'd19545e1-7ded-43d1-b1cb-2537459ce621',
                'name' => 'Training Manager',
                'surname' => 'Isa Yamta',
                'email' => 'isa.yamta@binaniair.com',
                'phone' => '+2348036589440',
                'status' => 0,
                'email_verified_at' => null,
                'password' => Hash::make('password'),
                'remember_token' => Str::random(60),
                'created_at' => '2025-04-12 05:18:01',
                'updated_at' => '2025-04-12 05:18:01',
            ],
            [
                'uuid' => 'b477e669-211b-4d38-8690-bbd036de8927',
                'name' => 'Quality & Safety Manager',
                'surname' => 'Daniel Ewurum',
                'email' => 'Daniel.ewurum@binaniair.com',
                'phone' => '2349123221408',
                'status' => 0,
                'email_verified_at' => null,
                'password' => Hash::make('password'),
                'remember_token' => null,
                'created_at' => '2025-04-12 05:39:04',
                'updated_at' => '2025-04-12 05:39:04',
            ],
            [
                'uuid' => '78e38742-4cf1-43c6-96eb-f029e3bdb369',
                'name' => 'C.O.O',
                'surname' => 'Capt. Danraka',
                'email' => 'coo@binaniair.com',
                'phone' => '2343642419900',
                'status' => 0,
                'email_verified_at' => null,
                'password' => Hash::make('password'),
                'remember_token' => null,
                'created_at' => '2025-04-12 05:43:17',
                'updated_at' => '2025-04-12 05:43:17',
            ],
            [
                'uuid' => '65d005c6-3465-48de-94a6-693f0a84114e',
                'name' => 'Accountable Manager',
                'surname' => 'Mohammed Naibi',
                'email' => 'am@binaniair.com',
                'phone' => '2348056264336',
                'status' => 0,
                'email_verified_at' => null,
                'password' => Hash::make('password'),
                'remember_token' => null,
                'created_at' => '2025-04-12 05:49:39',
                'updated_at' => '2025-04-12 05:49:39',
            ],
            [
                'uuid' => '368f0205-5eb9-4e90-a658-e2b15feba330',
                'name' => 'Planning Manager',
                'surname' => 'Emmauel Omolei',
                'email' => 'Emmanuel.omolei@binaniair.com',
                'phone' => '2348035968667',
                'status' => 0,
                'email_verified_at' => null,
                'password' => Hash::make('password'),
                'remember_token' => null,
                'created_at' => '2025-04-14 09:17:19',
                'updated_at' => '2025-04-14 09:17:19',
            ],
            [
                'uuid' => 'a17f7c26-3a1f-4cce-9791-fb563a211251',
                'name' => 'Cabin Services Manager',
                'surname' => 'Badmus Abdulazeez Kolawole',
                'email' => 'badmus@binaniair.com',
                'phone' => '2348036130640',
                'status' => 0,
                'email_verified_at' => null,
                'password' => Hash::make('password'),
                'remember_token' => Str::random(60),
                'created_at' => '2025-04-12 06:12:11',
                'updated_at' => '2025-04-12 06:12:11',
            ],
            [
                'uuid' => '21a02a91-7350-45a9-919c-769925dd3a8e',
                'name' => 'Engineer',
                'surname' => 'Cesar Robleto',
                'email' => 'Cesar.robleto@binaniair.com',
                'phone' => '0050764781709',
                'status' => 0,
                'email_verified_at' => null,
                'password' => Hash::make('password'),
                'remember_token' => null,
                'created_at' => '2025-04-12 06:15:12',
                'updated_at' => '2025-04-12 06:15:12',
            ],
            [
                'uuid' => 'e53bf6b0-d7da-4054-85c6-2c9e8ef02a59',
                'name' => 'Engineer',
                'surname' => 'Andre Pocas',
                'email' => 'Andre.pocas@binaniair.com',
                'phone' => '+351917492662',
                'status' => 0,
                'email_verified_at' => null,
                'password' => Hash::make('password'),
                'remember_token' => Str::random(60),
                'created_at' => '2025-04-12 06:17:47',
                'updated_at' => '2025-04-12 06:17:47',
            ],
            [
                'uuid' => 'de0cfbec-c893-4350-b124-16282e49b5ab',
                'name' => 'Director of Continuing Airworthiness',
                'surname' => 'Abdulrashid Balarabe',
                'email' => 'Abdul.balarabe@binaniair.com',
                'phone' => '+2348035704857',
                'status' => 0,
                'email_verified_at' => null,
                'password' => Hash::make('password'),
                'remember_token' => null,
                'created_at' => '2025-04-12 06:24:13',
                'updated_at' => '2025-04-12 06:24:13',
            ],
            [
                'uuid' => '60fa60f6-08b7-4691-8f9a-2587dccef709',
                'name' => 'Ground Operations Manager',
                'surname' => 'Mohammed Mukhtar Hassan',
                'email' => 'Mukhtar.hassan@binaniair.com',
                'phone' => '+2348033537852',
                'status' => 0,
                'email_verified_at' => null,
                'password' => Hash::make('password'),
                'remember_token' => null,
                'created_at' => '2025-04-12 06:27:48',
                'updated_at' => '2025-04-12 06:27:48',
            ],
            [
                'uuid' => '20234058-5e5c-44ef-9b7d-0998cd369926',
                'name' => 'Store Supervisor',
                'surname' => 'Zubairu Sadiq',
                'email' => 'Zubairu.sadiq@binaniair.com',
                'phone' => '+2348035220930',
                'status' => 0,
                'email_verified_at' => null,
                'password' => Hash::make('password'),
                'remember_token' => null,
                'created_at' => '2025-04-12 06:38:36',
                'updated_at' => '2025-04-12 06:38:36',
            ],
            [
                'uuid' => 'cacb76d1-90e7-417d-8881-4318083b2e62',
                'name' => 'Chief Security Officer',
                'surname' => 'Adamu Ibrahim',
                'email' => 'Adamu.ibrahim@binaniair.com',
                'phone' => '+2348065409946',
                'status' => 0,
                'email_verified_at' => null,
                'password' => Hash::make('password'),
                'remember_token' => null,
                'created_at' => '2025-04-12 06:40:44',
                'updated_at' => '2025-04-12 06:40:44',
            ],
            [
                'uuid' => 'fb2cd7b1-beca-464c-a029-c71f42142ffe',
                'name' => 'Operations Manager Alhassan',
                'surname' => 'Uba Nasir',
                'email' => 'alhassan.uba@binaniair.com',
                'phone' => '+2348035907783',
                'status' => 0,
                'email_verified_at' => null,
                'password' => Hash::make('password'),
                'remember_token' => Str::random(60),
                'created_at' => '2025-04-12 06:46:12',
                'updated_at' => '2025-04-12 06:46:12',
            ],
            [
                'uuid' => '2a936933-c93d-45b8-9d4a-81c9035f61cd',
                'name' => 'F/O ',
                'surname' => 'Rosemary Sugh',
                'email' => 'Mary.sugh@binaniair.com',
                'phone' => '08166013805',
                'status' => 0,
                'email_verified_at' => null,
                'password' => Hash::make('password'),
                'remember_token' => null,
                'created_at' => '2025-04-12 06:57:04',
                'updated_at' => '2025-04-12 06:57:04',
            ],
            [
                'uuid' => '443679f6-d11a-441b-a5b7-08c2bc3fc55f',
                'name' => 'F/O',
                'surname' => 'Mubarak Tukur Modibbo',
                'email' => 'Mubarak.modibbo@binaniair.com',
                'phone' => '+2348030777504',
                'status' => 0,
                'email_verified_at' => null,
                'password' => Hash::make('password'),
                'remember_token' => null,
                'created_at' => '2025-04-12 07:01:18',
                'updated_at' => '2025-04-12 07:01:18',
            ],
            [
                'uuid' => '7e3e115b-5714-4a51-bf1a-52a575f3bb98',
                'name' => 'Capt.',
                'surname' => 'Ahmed S. Mohammed',
                'email' => 'Ahmed.mohammed@binaniair.com',
                'phone' => '+2348038882298',
                'status' => 0,
                'email_verified_at' => null,
                'password' => Hash::make('password'),
                'remember_token' => Str::random(60),
                'created_at' => '2025-04-12 07:09:58',
                'updated_at' => '2025-04-12 07:09:58',
            ],
            [
                'uuid' => '8659a7f4-7864-4c10-9cdf-db688d4e3166',
                'name' => 'I.T Manager',
                'surname' => 'Abubakar Abdu',
                'email' => 'Abubakar.abdu@binaniair.com',
                'phone' => '+2349075938249',
                'status' => 0,
                'email_verified_at' => null,
                'password' => Hash::make('password'),
                'remember_token' => null,
                'created_at' => '2025-04-12 07:12:25',
                'updated_at' => '2025-04-12 07:12:25',
            ],
            [
                'uuid' => '9f04d24c-270f-4f39-bd53-025758f5ae63',
                'name' => 'Engr.',
                'surname' => 'Mohammed Kassab',
                'email' => 'mohammed.kassab@binaniair.com',
                'phone' => '+201068886854',
                'status' => 0,
                'email_verified_at' => null,
                'password' => Hash::make('password'),
                'remember_token' => null,
                'created_at' => '2025-04-16 07:36:53',
                'updated_at' => '2025-04-16 07:36:53',
            ],
            [
                'uuid' => 'bdbadb6f-3032-4bd9-a2b8-2d3946f62211',
                'name' => 'Engr.',
                'surname' => 'Fernando Gonzalez',
                'email' => 'fernando.gonzalez@binaniair.com',
                'phone' => '+50760263438',
                'status' => 0,
                'email_verified_at' => null,
                'password' => Hash::make('password'),
                'remember_token' => null,
                'created_at' => '2025-04-16 07:45:01',
                'updated_at' => '2025-04-16 07:45:01',
            ],
            [
                'uuid' => 'cb575c44-185a-41a5-9ae5-c0abfc8854b8',
                'name' => 'Chief Accountant',
                'surname' => 'Abubakar Gabdo',
                'email' => 'abubakar.gabdo@binaniair.com',
                'phone' => '2348034961689',
                'status' => 0,
                'email_verified_at' => null,
                'password' => Hash::make('password'),
                'remember_token' => null,
                'created_at' => '2025-04-16 07:54:43',
                'updated_at' => '2025-04-16 07:54:43',
            ],
            [
                'uuid' => '27c05c02-461d-4f88-adc7-fe321b15827a',
                'name' => 'Admin/ HR Manager',
                'surname' => 'Magaji Mohammed Misau',
                'email' => 'misau.mohammed@binaniair.com',
                'phone' => '07032913902',
                'status' => 0,
                'email_verified_at' => null,
                'password' => Hash::make('password'),
                'remember_token' => null,
                'created_at' => '2025-04-16 08:02:30',
                'updated_at' => '2025-04-16 08:02:30',
            ],
        ];
        foreach ($users as $userData) {
            $user = User::create($userData); // Assumes you are using the User model.
            $user->assignRole('user');
//            if (!$user->hasPermissionTo(['view-manual'])) {
                $user->givePermissionTo(['view-manual']);
//            }
        }
    }
}
