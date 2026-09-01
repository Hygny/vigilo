<?php

declare(strict_types=1);

use App\Jobs\ImportCompaniesCsvJob;
use App\Models\Organization;
use App\Models\Portfolio;
use App\Models\User;
use App\Notifications\CompaniesImported;
use Illuminate\Support\Facades\Notification;

it('imports from a file, cleans up the upload and notifies the user', function () {
    Notification::fake();

    $org = Organization::factory()->create();
    $user = User::factory()->for($org)->create();
    $portfolio = Portfolio::factory()->for($org)->create();

    $path = tempnam(sys_get_temp_dir(), 'vigilo_csv');
    file_put_contents($path, "cnpj,label\n11.444.777/0001-61,Cliente A\n123,Ruim\n");

    ImportCompaniesCsvJob::dispatchSync($portfolio, $path, $user->id);

    expect($portfolio->monitoredCompanies()->count())->toBe(1)
        ->and(is_file($path))->toBeFalse();

    Notification::assertSentTo($user, CompaniesImported::class, function (CompaniesImported $notification): bool {
        $data = $notification->toArray(new User);

        return $data['imported'] === 1 && $data['rejected'] === 1;
    });
});
