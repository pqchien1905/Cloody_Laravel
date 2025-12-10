<?php

namespace Database\Seeders;

use App\Models\Group;
use App\Models\User;
use Illuminate\Database\Seeder;

class GroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();

        if ($users->count() < 2) {
            $this->command->warn('Cần ít nhất 2 người dùng để tạo nhóm demo. Vui lòng chạy UserSeeder trước.');
            return;
        }

        // Nhóm 1: Nhóm Công việc (Private)
        $workGroup = Group::create([
            'name' => 'Nhóm Dự án CloudBox',
            'description' => 'Nhóm làm việc cho dự án CloudBox - Quản lý tài liệu và chia sẻ file nội bộ',
            'owner_id' => $users->first()->id,
            'privacy' => 'private',
        ]);

        // Thêm thành viên vào nhóm công việc
        $workGroup->members()->attach($users->first()->id, [
            'role' => 'admin',
            'joined_at' => now()->subDays(30),
        ]);

        if ($users->count() >= 2) {
            $workGroup->members()->attach($users->skip(1)->take(2)->pluck('id'), [
                'role' => 'member',
                'joined_at' => now()->subDays(20),
            ]);
        }

        // Nhóm 2: Nhóm Học tập (Public)
        $studyGroup = Group::create([
            'name' => 'Nhóm Học Laravel',
            'description' => 'Cộng đồng học Laravel và chia sẻ kinh nghiệm lập trình web. Mọi người đều có thể tham gia!',
            'owner_id' => $users->count() >= 2 ? $users->skip(1)->first()->id : $users->first()->id,
            'privacy' => 'public',
        ]);

        $studyGroup->members()->attach($studyGroup->owner_id, [
            'role' => 'admin',
            'joined_at' => now()->subDays(25),
        ]);

        if ($users->count() >= 3) {
            $studyGroup->members()->attach($users->skip(2)->take(2)->pluck('id'), [
                'role' => 'member',
                'joined_at' => now()->subDays(15),
            ]);
        }

        // Nhóm 3: Nhóm Design (Public)
        $designGroup = Group::create([
            'name' => 'Design & UI/UX',
            'description' => 'Nhóm chia sẻ tài nguyên thiết kế, mockup và thảo luận về UI/UX',
            'owner_id' => $users->first()->id,
            'privacy' => 'public',
        ]);

        $designGroup->members()->attach($users->first()->id, [
            'role' => 'admin',
            'joined_at' => now()->subDays(15),
        ]);

        if ($users->count() >= 4) {
            $designGroup->members()->attach($users->skip(3)->first()->id, [
                'role' => 'admin',
                'joined_at' => now()->subDays(10),
            ]);
        }

        // Nhóm 4: Nhóm Marketing (Private)
        $marketingGroup = Group::create([
            'name' => 'Marketing Team',
            'description' => 'Nhóm Marketing - Lên kế hoạch và chia sẻ tài liệu chiến dịch',
            'owner_id' => $users->count() >= 2 ? $users->skip(1)->first()->id : $users->first()->id,
            'privacy' => 'private',
        ]);

        $marketingGroup->members()->attach($marketingGroup->owner_id, [
            'role' => 'admin',
            'joined_at' => now()->subDays(10),
        ]);

        $this->command->info('✓ Đã tạo 4 nhóm demo với thành viên');
    }
}
