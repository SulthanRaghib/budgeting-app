<div class="overflow-x-auto">
    <table class="w-full table-auto text-sm">
        <thead>
            <tr class="text-left text-xs text-gray-500 uppercase">
                <th class="px-2 py-2">Account</th>
                <th class="px-2 py-2">Type</th>
                <th class="px-2 py-2 text-right">Balance</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($this->getAccounts() as $acc)
                <tr class="border-t">
                    <td class="px-2 py-2 font-medium">{{ $acc->name }}</td>
                    <td class="px-2 py-2"><span
                            class="inline-block px-2 py-1 text-xs rounded bg-gray-100">{{ $acc->type }}</span></td>
                    <td class="px-2 py-2 text-right">Rp {{ number_format((float) $acc->current_balance, 0, ',', '.') }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
