<?php

namespace App\Services;

use App\Models\Member;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MemberService
{
    public function save(Member $member, array $data, array $skillIds = [], array $roleIds = []): Member
    {
        return DB::transaction(function () use ($member, $data, $skillIds, $roleIds) {
            $member->fill($data)->save();
            $member->skills()->sync($skillIds);
            $member->professionalRoles()->sync($roleIds);

            return $member;
        });
    }

    public function delete(Member $member): void
    {
        DB::transaction(function () use ($member) {
            if ($user = $member->user) {
                $user->roles()->detach();          // cabut role (destruktif, restore harus manual)
                $user->permissions = [];           // cabut permission langsung
                $user->remember_token = Str::random(60); // matikan "ingat saya"
                $user->save();

                // buang sesi aktif (jika SESSION_DRIVER=database)
                try {
                    DB::table('sessions')->where('user_id', $user->id)->delete();
                } catch (\Exception $e) {
                    // Abaikan jika tabel sessions tidak ada
                }
            }

            $member->delete(); // soft delete
        });
    }

    public function restore(int $id): void
    {
        $member = Member::withTrashed()->findOrFail($id);
        
        DB::transaction(function () use ($member) {
            $member->restore();
            
            // Catatan: Role tidak dikembalikan otomatis karena detach() bersifat destruktif.
            // Admin/Pengurus harus memberikan role kembali secara manual lewat menu User.
        });
    }

    public function updateProfile(Member $member, array $data): Member
    {
        $member->update($data);

        return $member;
    }
}
