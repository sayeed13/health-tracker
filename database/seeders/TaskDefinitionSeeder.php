<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\TaskDefinition;

class TaskDefinitionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // আগের সব ডেটা মুছে নতুন করে ঢোকাই
        //TaskDefinitions::truncate();

        $tasks = [

            // ============================================
            // সকাল — ঘুম থেকে উঠা
            // ============================================
            [
                'category'       => 'water',
                'title'          => 'ঘুম থেকে উঠুন',
                'description'    => 'এক গ্লাস কুসুম গরম পানি পান করুন',
                'scheduled_time' => '08:00:00',
                'repeat_type'    => 'daily',
                'repeat_days'    => null,
                'active_from_day'=> 0,
                'active_until_day'=> null,
                'sort_order'     => 1,
                'is_active'      => true,
            ],

            // ============================================
            // নামাজ
            // ============================================
            [
                'category'       => 'prayer',
                'title'          => 'ফজরের নামাজ',
                'description'    => 'ফজরের নামাজ পড়ুন',
                'scheduled_time' => '08:15:00',
                'repeat_type'    => 'daily',
                'repeat_days'    => null,
                'active_from_day'=> 0,
                'active_until_day'=> null,
                'sort_order'     => 2,
                'is_active'      => true,
            ],
            [
                'category'       => 'prayer',
                'title'          => 'জোহরের নামাজ',
                'description'    => 'জোহরের নামাজ পড়ুন',
                'scheduled_time' => '12:30:00',
                'repeat_type'    => 'daily',
                'repeat_days'    => null,
                'active_from_day'=> 0,
                'active_until_day'=> null,
                'sort_order'     => 7,
                'is_active'      => true,
            ],
            [
                'category'       => 'prayer',
                'title'          => 'আসরের নামাজ',
                'description'    => 'আসরের নামাজ পড়ুন',
                'scheduled_time' => '16:30:00',
                'repeat_type'    => 'daily',
                'repeat_days'    => null,
                'active_from_day'=> 0,
                'active_until_day'=> null,
                'sort_order'     => 9,
                'is_active'      => true,
            ],
            [
                'category'       => 'prayer',
                'title'          => 'মাগরিবের নামাজ',
                'description'    => 'মাগরিবের নামাজ পড়ুন',
                'scheduled_time' => '19:00:00',
                'repeat_type'    => 'daily',
                'repeat_days'    => null,
                'active_from_day'=> 0,
                'active_until_day'=> null,
                'sort_order'     => 11,
                'is_active'      => true,
            ],
            [
                'category'       => 'prayer',
                'title'          => 'এশার নামাজ',
                'description'    => 'এশার নামাজ পড়ুন',
                'scheduled_time' => '21:00:00',
                'repeat_type'    => 'daily',
                'repeat_days'    => null,
                'active_from_day'=> 0,
                'active_until_day'=> null,
                'sort_order'     => 14,
                'is_active'      => true,
            ],
            [
                'category'       => 'prayer',
                'title'          => 'জুমার নামাজ',
                'description'    => 'জুমার নামাজ পড়ুন — বিশেষ দিন',
                'scheduled_time' => '13:00:00',
                'repeat_type'    => 'weekly',
                'repeat_days'    => [5], // শুক্রবার
                'active_from_day'=> 0,
                'active_until_day'=> null,
                'sort_order'     => 8,
                'is_active'      => true,
            ],

            // ============================================
            // ব্যায়াম — সপ্তাহের নির্দিষ্ট দিন
            // ============================================
            [
                'category'       => 'exercise',
                'title'          => 'কার্ডিও — হাঁটা বা জগিং',
                'description'    => '৩০ মিনিট বাইরে হাঁটা বা জগিং করুন',
                'scheduled_time' => '08:30:00',
                'repeat_type'    => 'weekly',
                'repeat_days'    => [2, 6], // মঙ্গলবার ও শনিবার
                'active_from_day'=> 0,
                'active_until_day'=> null,
                'sort_order'     => 3,
                'is_active'      => true,
            ],
            [
                'category'       => 'exercise',
                'title'          => 'শক্তি বৃদ্ধি — বডিওয়েট',
                'description'    => 'Push-up, Squat, Plank, Jumping Jack — ৩০ মিনিট',
                'scheduled_time' => '08:30:00',
                'repeat_type'    => 'weekly',
                'repeat_days'    => [0, 3], // রবিবার ও বুধবার
                'active_from_day'=> 0,
                'active_until_day'=> null,
                'sort_order'     => 3,
                'is_active'      => true,
            ],
            [
                'category'       => 'exercise',
                'title'          => 'স্ট্রেচিং ও বিশ্রাম',
                'description'    => '২০ মিনিট হালকা স্ট্রেচিং ও ৫ মিনিট গভীর শ্বাস',
                'scheduled_time' => '08:30:00',
                'repeat_type'    => 'weekly',
                'repeat_days'    => [1, 4], // সোমবার ও বৃহস্পতিবার
                'active_from_day'=> 0,
                'active_until_day'=> null,
                'sort_order'     => 3,
                'is_active'      => true,
            ],

            // ============================================
            // ওষুধ
            // ============================================
            [
                'category'       => 'medicine',
                'title'          => 'TB ওষুধ খান',
                'description'    => 'TB ওষুধ খালি পেটে খান',
                'scheduled_time' => '08:00:00',
                'repeat_type'    => 'daily',
                'repeat_days'    => null,
                'active_from_day'=> 0,
                'active_until_day'=> 180, // ৬ মাস
                'sort_order'     => 4,
                'is_active'      => true,
            ],
            [
                'category'       => 'medicine',
                'title'          => 'ART ওষুধ নিন',
                'description'    => 'TDF+3TC+DTG — প্রতিদিন রাত ৮টায়',
                'scheduled_time' => '20:00:00',
                'repeat_type'    => 'daily',
                'repeat_days'    => null,
                'active_from_day'=> 0,
                'active_until_day'=> null,
                'sort_order'     => 13,
                'is_active'      => true,
            ],
            [
                'category'       => 'medicine',
                'title'          => 'Extra DTG ওষুধ নিন',
                'description'    => 'Extra DTG — প্রতিদিন রাত ৮টায়',
                'scheduled_time' => '10:00:00',
                'repeat_type'    => 'daily',
                'repeat_days'    => null,
                'active_from_day'=> 0,
                'active_until_day'=> 180,
                'sort_order'     => 13,
                'is_active'      => true,
            ],
            // ============================================
            // ধূমপান — smoking habits
            // ============================================
            [
                'category'        => 'smoking',
                'title'           => 'সকালে উঠে সিগারেট',
                'description'     => 'সকালে উঠে সিগারেট খেয়েছি কি?',
                'scheduled_time'  => '08:00:00',
                'repeat_type'     => 'daily',
                'repeat_days'     => null,
                'active_from_day' => 0,
                'active_until_day'=> null,
                'sort_order'      => 1,
                'is_active'       => true,
            ],
            [
                'category'        => 'smoking',
                'title'           => 'নাস্তার পর সিগারেট',
                'description'     => 'নাস্তার পর সিগারেট খেয়েছি কি?',
                'scheduled_time'  => '11:30:00',
                'repeat_type'     => 'daily',
                'repeat_days'     => null,
                'active_from_day' => 0,
                'active_until_day'=> null,
                'sort_order'      => 2,
                'is_active'       => true,
            ],
            [
                'category'        => 'smoking',
                'title'           => 'দুপুরের খাবারের পর সিগারেট',
                'description'     => 'দুপুরের খাবারের পর সিগারেট খেয়েছি কি?',
                'scheduled_time'  => '15:30:00',
                'repeat_type'     => 'daily',
                'repeat_days'     => null,
                'active_from_day' => 0,
                'active_until_day'=> null,
                'sort_order'      => 3,
                'is_active'       => true,
            ],
            [
                'category'        => 'smoking',
                'title'           => 'সন্ধ্যার ব্রেকে সিগারেট',
                'description'     => 'সন্ধ্যার ব্রেকে সিগারেট খেয়েছি কি?',
                'scheduled_time'  => '19:00:00',
                'repeat_type'     => 'daily',
                'repeat_days'     => null,
                'active_from_day' => 0,
                'active_until_day'=> null,
                'sort_order'      => 4,
                'is_active'       => true,
            ],
            [
                'category'        => 'smoking',
                'title'           => 'অফিস শেষে সিগারেট',
                'description'     => 'অফিস শেষে সিগারেট খেয়েছি কি?',
                'scheduled_time'  => '22:00:00',
                'repeat_type'     => 'daily',
                'repeat_days'     => null,
                'active_from_day' => 0,
                'active_until_day'=> null,
                'sort_order'      => 5,
                'is_active'       => true,
            ],
            [
                'category'        => 'smoking',
                'title'           => 'রাতের খাবারের পর সিগারেট',
                'description'     => 'রাতের খাবারের পর সিগারেট খেয়েছি কি?',
                'scheduled_time'  => '00:30:00',
                'repeat_type'     => 'daily',
                'repeat_days'     => null,
                'active_from_day' => 0,
                'active_until_day'=> null,
                'sort_order'      => 6,
                'is_active'       => true,
            ],
        ];

        foreach ($tasks as $task) {
            TaskDefinition::create($task);
        }

        $this->command->info('✅ ' . count($tasks) . 'টি টাস্ক সফলভাবে যোগ করা হয়েছে!');
    }
}
