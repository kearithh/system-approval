<?php

use Illuminate\Database\Seeder;

class CompanyTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $data = [
            [
                'name' => 'អេសធីអេសខេ(STSK)',
                'long_name' => 'អេសធីអេសខេ អុីនវេសមិន គ្រុប លីមីធីត',
                'logo' => '/img/logo/stsk.png',
                'footer' => '/img/logo/footer_stsk.png',
                'footer_landscape' => '/img/logo/footer_mfi.png',
                'type' => '3', //3=STSK &Clinic,
            ],
            [
                'name' => 'សហគ្រិនភាព(MFI)',
                'long_name' => 'សហគ្រិនភាព មា៉យក្រូហ្វាយនែន ភីអិលស៊ី',
                'logo' => '/img/logo/mfi.png',
                'footer' => '/img/logo/footer_stsk.png',
                'footer_landscape' => '/img/logo/footer_mfi.png',
                'type' => '1', //1=MFI
            ],
            [
                'name' => 'សហគ្រិនភាព(NGO)',
                'long_name' => 'សហគ្រិនភាព',
                'logo' => '/img/logo/ngo.png',
                'footer' => '/img/logo/footer_ngo.png',
                'footer_landscape' => '/img/logo/footer_ngo_l.png',
                'type' => '2', //2=NGO
            ],
            [
                'name' => 'អរៀនដា ',
                'long_name' => 'អរៀនដា',
                'logo' => '/img/logo/ord.png',
                'footer' => '/img/logo/footer_ord.png',
                'footer_landscape' => '/img/logo/footer_ord_l.png',
                'type' => '3', //3=STSK &Clinic
            ],
            [
                'name' => 'អេស ​ធី ​អេច​គ្លូស៊ីវ ​អាផាតមិន​​',
                'long_name' => 'អេស ​ធី ​អេច​គ្លូស៊ីវ ​អាផាតមិន​​',
                'logo' => '/img/logo/st.png',
                'footer' => '/img/logo/footer_st.png',
                'footer_landscape' => '/img/logo/footer_st_l.png',
                'type' => '5',  //5=ST
            ],
            [
                'name' => 'មេគង្គ ​ម៉ាយ​ក្រូ​អ៊ិន​សួរេន',
                'long_name' => 'មេគង្គ ​ម៉ាយ​ក្រូ​អ៊ិន​សួរេន',
                'logo' => '/img/logo/mmi.png',
                'footer' => '/img/logo/footer_mmi.png',
                'footer_landscape' => '/img/logo/footer_mmi_l.png',
                'type' => '3', //3=STSK &Clinic,
            ],
            [
                'name' => 'ស្ថានីយប្រេងឥន្ធនៈតេលាមហាមន្រ្តី',
                'long_name' => 'ស្ថានីយប្រេងឥន្ធនៈតេលាមហាមន្រ្តី',
                'logo' => '/img/logo/mht.png',
                'footer' => '/img/logo/footer_mht.png',
                'footer_landscape' => '/img/logo/footer_mht.png',
                'type' => '4', // 4=Tela
            ],
            [
                'name' => 'តេលាទួលស្វាយព្រៃ',
                'long_name' => 'តេលាទួលស្វាយព្រៃ',
                'logo' => '/img/logo/tsp.png',
                'footer' => '/img/logo/footer_tsp.png',
                'footer_landscape' => '/img/logo/footer_tsp.png',
                'type' => '4', // 4=Tela
            ],
        ];

        foreach ($data as $item) {
            \App\Company::create([
                'name' => $item['name'],
                'long_name' => $item['long_name'],
                'logo' => $item['logo'],
                'footer' => $item['footer'],
                'footer_landscape' => $item['footer_landscape'],
                'type' => $item['logo'],
            ]);
        }


    }
}
