<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Gig;
use App\Models\Order;
use App\Models\Review;
use App\Models\GigSlot;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\Badge;
use App\Models\Notification;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class EduGigsSeeder extends Seeder
{
    public function run(): void
    {
        // 1. THE TRINITY (Primary Demo Accounts)
        $demoSeller1 = User::create([
            'name' => 'Demo Seller One', 'nickname' => 'seller_one',
            'email' => 'seller1@siswa.unimas.my', 'password' => Hash::make('password123'),
            'role' => 'seller', 'faculty' => 'FCSIT - Software Engineering',
            'bio' => 'Final Year SE student. Java expert, Web Dev enthusiast, and part-time hardware technician.',
        ]);

        $demoSeller2 = User::create([
            'name' => 'Demo Seller Two', 'nickname' => 'seller_two',
            'email' => 'seller2@siswa.unimas.my', 'password' => Hash::make('password123'),
            'role' => 'seller', 'faculty' => 'FEB - Marketing',
            'bio' => 'Professional photography & creative design services.',
        ]);

        $demoBuyer1 = User::create([
            'name' => 'Demo Buyer One', 'nickname' => 'buyer_one',
            'email' => 'buyer1@siswa.unimas.my', 'password' => Hash::make('password123'),
            'role' => 'buyer', 'faculty' => 'FACA - Cinematography',
        ]);

        // 2. THE CAMPUS CITIZENS (Mass User Generation)
        $userData = [
            ['name' => 'Ahmad Zikri', 'nick' => 'zikri_dev', 'fac' => 'FCSIT - Information Systems', 'role' => 'seller'],
            ['name' => 'Nurul Izzah', 'nick' => 'izzah_cooks', 'fac' => 'FRST - Resource Biotech', 'role' => 'seller'],
            ['name' => 'Brandon Lim', 'nick' => 'brand_prints', 'fac' => 'FEB - Accounting', 'role' => 'seller'],
            ['name' => 'Siti Aminah', 'nick' => 'siti_tutor', 'fac' => 'FSSH - Sociology', 'role' => 'seller'],
            ['name' => 'Joshua Wong', 'nick' => 'josh_fix', 'fac' => 'FENG - Mechanical', 'role' => 'seller'],
            ['name' => 'Dayangku Syaza', 'nick' => 'syaza_mua', 'fac' => 'FACA - Design', 'role' => 'seller'],
            ['name' => 'Abang Haikal', 'nick' => 'haikal_runner', 'fac' => 'FLC - Linguistics', 'role' => 'seller'],
            ['name' => 'Michelle Tan', 'nick' => 'mich_design', 'fac' => 'FACA - Fine Arts', 'role' => 'seller'],
            ['name' => 'Ravi Shankar', 'nick' => 'ravi_code', 'fac' => 'FCSIT - Network', 'role' => 'buyer'],
            ['name' => 'Chai Mei Ling', 'nick' => 'meiling_lee', 'fac' => 'FEB - Economics', 'role' => 'buyer'],
            ['name' => 'Zulhelmi Ali', 'nick' => 'zul_fast', 'fac' => 'FENG - Civil', 'role' => 'buyer'],
            ['name' => 'Sarah Connor', 'nick' => 'terminator', 'fac' => 'FRST - Chemistry', 'role' => 'buyer'],
            ['name' => 'Jason Low', 'nick' => 'jason_l', 'fac' => 'FMHS - Nursing', 'role' => 'buyer'],
            ['name' => 'Fatimah Zahra', 'nick' => 'fatimah_z', 'fac' => 'FPSK - Medicine', 'role' => 'buyer'],
            ['name' => 'Wei Kang', 'nick' => 'wkang_99', 'fac' => 'FEB - Marketing', 'role' => 'buyer'],
            ['name' => 'Evelyn Rose', 'nick' => 'eve_r', 'fac' => 'FACA - Music', 'role' => 'buyer'],
            ['name' => 'Hafizuddin', 'nick' => 'hafiz_unimas', 'fac' => 'FENG - Electrical', 'role' => 'buyer'],
            ['name' => 'Grace Kelly', 'nick' => 'grace_k', 'fac' => 'FSSH - Psychology', 'role' => 'buyer'],
            ['name' => 'Zainal Abidin', 'nick' => 'zainal_a', 'fac' => 'FLC - Communication', 'role' => 'buyer'],
            ['name' => 'Taufiq Hidayat', 'nick' => 'taufiq_h', 'fac' => 'FEB - Finance', 'role' => 'buyer'],
            ['name' => 'Nur Aliyah', 'nick' => 'aliyah_n', 'fac' => 'FRST - Resource Biotech', 'role' => 'buyer'],
            ['name' => 'Kevin Durant', 'nick' => 'kd_easy', 'fac' => 'FCSIT - Software', 'role' => 'buyer'],
            ['name' => 'Syafiqah Redzuan', 'nick' => 'sya_red', 'fac' => 'FEB - Accounting', 'role' => 'buyer'],
            ['name' => 'Lau Siew Ping', 'nick' => 'ping_l', 'fac' => 'FENG - Mechanical', 'role' => 'buyer'],
            ['name' => 'Haris Jauhari', 'nick' => 'haris_j', 'fac' => 'FACA - Drama', 'role' => 'buyer'],
            ['name' => 'Chloe Grace', 'nick' => 'chloe_g', 'fac' => 'FMHS - Medicine', 'role' => 'buyer'],
            ['name' => 'Zarith Sofea', 'nick' => 'zarith_s', 'fac' => 'FSSH - History', 'role' => 'buyer'],
            ['name' => 'Lim Kok Wing', 'nick' => 'kokwing', 'fac' => 'FACA - Design', 'role' => 'buyer'],
            ['name' => 'Mohd Firdaus', 'nick' => 'firdaus_m', 'fac' => 'FCSIT - Network', 'role' => 'buyer'],
            ['name' => 'Nurul Ain', 'nick' => 'ain_n', 'fac' => 'FRST - Biology', 'role' => 'buyer'],
            ['name' => 'Darren Chen', 'nick' => 'darren_c', 'fac' => 'FEB - Marketing', 'role' => 'buyer'],
            ['name' => 'Siti Nurhaliza', 'nick' => 'ct_n', 'fac' => 'FACA - Music', 'role' => 'buyer'],
            ['name' => 'Badrul Muhayat', 'nick' => 'badrul_m', 'fac' => 'FENG - Civil', 'role' => 'buyer'],
            ['name' => 'Alia Bhatt', 'nick' => 'alia_b', 'fac' => 'FEB - Economics', 'role' => 'buyer'],
            ['name' => 'Tommy Vercetti', 'nick' => 'tommy_v', 'fac' => 'FCSIT - IS', 'role' => 'buyer'],
            ['name' => 'Cynthia Lau', 'nick' => 'cynth_l', 'fac' => 'FACA - Fine Arts', 'role' => 'buyer'],
            ['name' => 'Razali Ismail', 'nick' => 'razali_i', 'fac' => 'FLC - Communication', 'role' => 'buyer'],
        ];

        $users = [];
        foreach ($userData as $i => $data) {
            $users[] = User::create([
                'name' => $data['name'],
                'nickname' => $data['nick'],
                'email' => (91000 + $i) . "@siswa.unimas.my",
                'password' => Hash::make('password123'),
                'role' => $data['role'],
                'faculty' => $data['fac'],
            ]);
        }

        // 3. CORE GAMIFICATION BADGES & NOTIFICATIONS
        if (class_exists(Badge::class)) {
            $topRated = Badge::create(['name' => 'Top Rated', 'slug' => 'top-rated', 'description' => 'Consistently delivers high-quality service.', 'icon_class' => 'bg-yellow-100 text-yellow-600']);
            $fastResponder = Badge::create(['name' => 'Fast Responder', 'slug' => 'fast-responder', 'description' => 'Replies to messages within an hour.', 'icon_class' => 'bg-blue-100 text-blue-600']);
            $trusted = Badge::create(['name' => 'Trusted Seller', 'slug' => 'trusted-seller', 'description' => 'Verified history of successful transactions.', 'icon_class' => 'bg-green-100 text-green-600']);

            DB::table('badge_user')->insert([
                ['user_id' => $demoSeller1->id, 'badge_id' => $topRated->id, 'created_at' => Carbon::now()->subDays(5), 'updated_at' => Carbon::now()->subDays(5)],
                ['user_id' => $demoSeller1->id, 'badge_id' => $trusted->id, 'created_at' => Carbon::now()->subDays(2), 'updated_at' => Carbon::now()->subDays(2)],
                ['user_id' => $demoSeller2->id, 'badge_id' => $topRated->id, 'created_at' => Carbon::now()->subDays(4), 'updated_at' => Carbon::now()->subDays(4)],
                ['user_id' => $demoSeller2->id, 'badge_id' => $fastResponder->id, 'created_at' => Carbon::now()->subDays(1), 'updated_at' => Carbon::now()->subDays(1)]
            ]);

            if (class_exists(Notification::class)) {
                Notification::create(['user_id' => $demoSeller1->id, 'title' => 'Badge Earned!', 'message' => 'Congratulations! You have earned the Trusted Seller badge.', 'type' => 'success', 'read' => true, 'created_at' => Carbon::now()->subDays(2), 'updated_at' => Carbon::now()->subDays(2)]);
                Notification::create(['user_id' => $demoSeller2->id, 'title' => 'Badge Earned!', 'message' => 'Congratulations! You have earned the Top Rated badge.', 'type' => 'success', 'read' => true, 'created_at' => Carbon::now()->subDays(5), 'updated_at' => Carbon::now()->subDays(5)]);
                Notification::create(['user_id' => $demoSeller2->id, 'title' => 'Badge Earned!', 'message' => 'Congratulations! You have earned the Fast Responder badge.', 'type' => 'success', 'read' => true, 'created_at' => Carbon::now()->subDays(1), 'updated_at' => Carbon::now()->subDays(1)]);
            }
        }

        // 4. CORE GIGS
        $gigJava = Gig::create(['user_id' => $demoSeller1->id, 'title' => 'Java & Web Dev Tutoring', 'category' => 'Tutoring', 'price' => 30.00, 'scheduling_type' => 'specific', 'status' => 'active', 'description' => 'Help with OOP, Polymorphism, and Vue.js lab assignments. Complete walkthrough provided.']);
        $gigPrint = Gig::create(['user_id' => $demoSeller1->id, 'title' => 'Fast Campus Printing', 'category' => 'Printing', 'price' => 0.50, 'scheduling_type' => 'always', 'status' => 'active', 'description' => 'High quality laser printing. Located near FCSIT. Available for quick pickups.']);
        $gigPc = Gig::create(['user_id' => $demoSeller1->id, 'title' => 'Laptop Dust Cleaning', 'category' => 'Technical', 'price' => 45.00, 'scheduling_type' => 'specific', 'status' => 'active', 'description' => 'Internal fan cleaning and thermal paste replacement using Arctic MX-4.']);
        
        $gigPhoto = Gig::create(['user_id' => $demoSeller2->id, 'title' => 'Portrait Photography', 'category' => 'Creative', 'price' => 80.00, 'scheduling_type' => 'specific', 'status' => 'active', 'description' => 'Professional outdoor graduation or profile shoots around UNIMAS. 10 edited photos.']);
        $gigMua = Gig::create(['user_id' => $demoSeller2->id, 'title' => 'Event Makeup Service', 'category' => 'Creative', 'price' => 60.00, 'scheduling_type' => 'always', 'status' => 'active', 'description' => 'Elegant makeup styles for dinner nights and convocations. Using premium products.']);

        // 5. SPECIFIC DEMO DATA (Slots, Orders, Chat & Notifications)
        $javaSlot = GigSlot::create([
            'gig_id' => $gigJava->id,
            'start_time' => Carbon::tomorrow()->setTime(10, 0),
            'is_booked' => true
        ]);

        $pendingOrder = Order::create([
            'buyer_id' => $demoSeller2->id, 
            'gig_id' => $gigJava->id, 
            'slot_id' => $javaSlot->id, 
            'status' => 'pending', 
            'final_price' => 30.00, 
            'created_at' => Carbon::now()->subHours(2), 
            'updated_at' => Carbon::now()->subHours(2)
        ]);

        if (class_exists(Notification::class)) {
            Notification::create(['user_id' => $demoSeller1->id, 'title' => 'New Order Request', 'message' => 'Demo Seller Two requested Java & Web Dev Tutoring.', 'type' => 'info', 'read' => false, 'created_at' => Carbon::now()->subHours(2), 'updated_at' => Carbon::now()->subHours(2)]);
            Notification::create(['user_id' => $demoSeller2->id, 'title' => 'Order Placed', 'message' => 'You requested Java & Web Dev Tutoring from Demo Seller One.', 'type' => 'info', 'read' => true, 'created_at' => Carbon::now()->subHours(2), 'updated_at' => Carbon::now()->subHours(2)]);
        }
        
        $photoSlot = GigSlot::create([
            'gig_id' => $gigPhoto->id,
            'start_time' => Carbon::now()->addDays(2)->setTime(9, 0),
            'is_booked' => true
        ]);

        $acceptedOrder = Order::create([
            'buyer_id' => $demoSeller1->id, 
            'gig_id' => $gigPhoto->id, 
            'slot_id' => $photoSlot->id, 
            'status' => 'accepted', 
            'final_price' => 80.00, 
            'created_at' => Carbon::now()->subDays(1), 
            'updated_at' => Carbon::now()->subDays(1)
        ]);

        if (class_exists(Notification::class)) {
            Notification::create(['user_id' => $demoSeller1->id, 'title' => 'Order Accepted', 'message' => 'Demo Seller Two accepted your request for Portrait Photography.', 'type' => 'success', 'read' => true, 'created_at' => Carbon::now()->subDays(1)->addMinutes(15), 'updated_at' => Carbon::now()->subDays(1)->addMinutes(15)]);
            Notification::create(['user_id' => $demoSeller2->id, 'title' => 'Order Accepted', 'message' => 'You accepted Demo Seller One\'s request for Portrait Photography.', 'type' => 'success', 'read' => true, 'created_at' => Carbon::now()->subDays(1)->addMinutes(15), 'updated_at' => Carbon::now()->subDays(1)->addMinutes(15)]);
        }

        Order::create(['buyer_id' => $users[0]->id, 'gig_id' => $gigPc->id, 'status' => 'accepted', 'final_price' => 45.00, 'created_at' => Carbon::now()->subHours(5), 'updated_at' => Carbon::now()->subHours(5)]);

        if (class_exists(Notification::class)) {
            Notification::create(['user_id' => $demoSeller1->id, 'title' => 'New Order Request', 'message' => $users[0]->name . ' requested Laptop Dust Cleaning.', 'type' => 'info', 'read' => true, 'created_at' => Carbon::now()->subHours(5), 'updated_at' => Carbon::now()->subHours(5)]);
            Notification::create(['user_id' => $users[0]->id, 'title' => 'Order Placed', 'message' => 'You requested Laptop Dust Cleaning from Demo Seller One.', 'type' => 'info', 'read' => true, 'created_at' => Carbon::now()->subHours(5), 'updated_at' => Carbon::now()->subHours(5)]);
        }

        if (class_exists(Conversation::class) && class_exists(Message::class)) {
            $conv1 = Conversation::create([
                'order_id' => $pendingOrder->id,
                'sender_id' => $demoSeller2->id,
                'receiver_id' => $demoSeller1->id,
                'created_at' => Carbon::now()->subHours(2),
                'updated_at' => Carbon::now()->subHours(2)
            ]);

            Message::create(['conversation_id' => $conv1->id, 'sender_id' => $demoSeller2->id, 'body' => 'Hey! I just requested a tutoring session for tomorrow.', 'created_at' => Carbon::now()->subHours(2)]);
            Message::create(['conversation_id' => $conv1->id, 'sender_id' => $demoSeller1->id, 'body' => 'Hi! Got it. I see the pending order on my dashboard.', 'created_at' => Carbon::now()->subMinutes(115)]);

            if (class_exists(Notification::class)) {
                Notification::create(['user_id' => $demoSeller1->id, 'title' => 'New Message', 'message' => 'Demo Seller Two sent you a new message.', 'type' => 'info', 'read' => false, 'created_at' => Carbon::now()->subMinutes(5), 'updated_at' => Carbon::now()->subMinutes(5)]);
                Notification::create(['user_id' => $demoSeller2->id, 'title' => 'New Message', 'message' => 'Demo Seller One replied to your chat.', 'type' => 'info', 'read' => true, 'created_at' => Carbon::now()->subMinutes(30), 'updated_at' => Carbon::now()->subMinutes(30)]);
            }

            $conv2 = Conversation::create([
                'order_id' => $acceptedOrder->id,
                'sender_id' => $demoSeller1->id,
                'receiver_id' => $demoSeller2->id,
                'created_at' => Carbon::now()->subDays(1),
                'updated_at' => Carbon::now()->subDays(1)
            ]);

            Message::create(['conversation_id' => $conv2->id, 'sender_id' => $demoSeller1->id, 'body' => 'Hi, are we still on for the photoshoot this weekend?', 'created_at' => Carbon::now()->subHours(20)]);
            Message::create(['conversation_id' => $conv2->id, 'sender_id' => $demoSeller2->id, 'body' => 'Yep! I already accepted your order.', 'created_at' => Carbon::now()->subHours(19)]);
            
            if (class_exists(Notification::class)) {
                Notification::create(['user_id' => $demoSeller1->id, 'title' => 'New Message', 'message' => 'Demo Seller Two replied to your chat.', 'type' => 'info', 'read' => false, 'created_at' => Carbon::now()->subHours(4), 'updated_at' => Carbon::now()->subHours(4)]);
                Notification::create(['user_id' => $demoSeller2->id, 'title' => 'New Message', 'message' => 'Demo Seller One sent you a new message.', 'type' => 'info', 'read' => true, 'created_at' => Carbon::now()->subHours(5), 'updated_at' => Carbon::now()->subHours(5)]);
            }
        }

        // 6. GENERATE PAST COMPLETED ORDERS & REVIEWS
        for ($i = 0; $i < 12; $i++) {
            $buyer = $users[array_rand($users)];
            $o = Order::create(['buyer_id' => $buyer->id, 'gig_id' => $gigPrint->id, 'status' => 'completed', 'final_price' => rand(5, 25), 'updated_at' => Carbon::now()->subDays(rand(1, 20))]);
            
            $isFlagged = (rand(1, 10) > 8) ? true : false; 
            Review::create(['gig_id' => $gigPrint->id, 'user_id' => $buyer->id, 'order_id' => $o->id, 'rating' => rand(4, 5), 'comment' => 'Very fast and convenient!', 'is_flagged' => $isFlagged]);
        }
        
        for ($i = 0; $i < 9; $i++) {
            $buyer = $users[array_rand($users)];
            $gig = (rand(1, 10) > 5) ? $gigPhoto : $gigMua; 
            
            $o = Order::create(['buyer_id' => $buyer->id, 'gig_id' => $gig->id, 'status' => 'completed', 'final_price' => $gig->price, 'updated_at' => Carbon::now()->subDays(rand(1, 20))]);
            
            Review::create(['gig_id' => $gig->id, 'user_id' => $buyer->id, 'order_id' => $o->id, 'rating' => rand(4, 5), 'comment' => 'Amazing work! Highly recommended.', 'is_flagged' => false]);
        }

        // 7. BACKGROUND SELLER GIGS
        $titles = [
            'Tutoring' => ['Calculus 1 Revision', 'Matlab Programming Help', 'Accounting 101', 'Data Structures in C++', 'Physics 101 Crash Course', 'Basic Mandarin Practice'],
            'Delivery' => ['Campus Runner', 'Cafeteria Food Delivery', 'Laundry Pickup Service', 'Grocery Runner', 'Station Dropoff'],
            'Creative' => ['Logo Design for Clubs', 'Video Editing Service', 'Infographic Design', 'PowerPoint Polishing', 'Portrait Illustration', 'Social Media Management'],
            'Technical' => ['Windows Reformat', 'Software Installation', 'Network Troubleshooting', 'Data Recovery Help', 'PC Build Assembly', 'Malware Removal']
        ];

        foreach ($users as $user) {
            if ($user->role === 'seller') {
                for ($j = 0; $j < rand(2, 3); $j++) {
                    $cat = array_rand($titles);
                    $title = $titles[$cat][array_rand($titles[$cat])];
                    
                    $bgGig = Gig::create([
                        'user_id' => $user->id,
                        'title' => $title,
                        'category' => $cat,
                        'price' => rand(10, 50),
                        'scheduling_type' => 'always',
                        'status' => 'active', 
                        'description' => "Verified student $cat service for the university community. Professional and reliable."
                    ]);

                    for ($k = 0; $k < rand(2, 5); $k++) {
                        $b = $users[array_rand($users)];
                        $date = Carbon::now()->subDays(rand(10, 60));
                        $bo = Order::create(['buyer_id' => $b->id, 'gig_id' => $bgGig->id, 'status' => 'completed', 'final_price' => $bgGig->price, 'updated_at' => $date, 'created_at' => $date]);
                        Review::create(['gig_id' => $bgGig->id, 'user_id' => $b->id, 'order_id' => $bo->id, 'rating' => rand(4, 5), 'comment' => 'Great service, would book again!']);
                    }
                }
            }
        }
    }
}