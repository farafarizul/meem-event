<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class UserExport implements FromQuery, WithHeadings, WithMapping
{
    private ?string $search;

    public function __construct(?string $search = null)
    {
        $this->search = $search;
    }

    public function query()
    {
        $q = User::where('is_admin', false);

        if ($this->search) {
            $term = $this->search;
            $q->where(function ($query) use ($term) {
                $query->where('fullname', 'like', "%{$term}%")
                    ->orWhere('meem_code', 'like', "%{$term}%")
                    ->orWhere('phone_number', 'like', "%{$term}%")
                    ->orWhere('email', 'like', "%{$term}%");
            });
        }

        return $q;
    }

    public function headings(): array
    {
        return ['#', 'Meem Code', 'Meem ID', 'Full Name', 'Phone Number', 'Email', 'Registered At'];
    }

    public function map($user): array
    {
        return [
            $user->id,
            $user->meem_code,
            $user->meem_id ?? '-',
            $user->fullname,
            $user->phone_number,
            $user->email ?? '-',
            $user->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
