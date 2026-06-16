<?php

namespace App\Console\Commands;

use App\Models\Visit;
use App\Models\Visitor;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class AnonymizeOldVisitorData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:anonymize-old-visitor-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Anonymize old visitor data based on the configured retention period';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $retentionDays = (int) config('retention.visitor_data_retention_days', 90);
        $cutoff = now()->subDays($retentionDays);

        $updatedVisits = Visit::query()
            ->whereNotNull('check_out_time')
            ->where('check_out_time', '<', $cutoff)
            ->update([
                'reason_of_visit' => null,
                'updated_at' => now(),
            ]);

        $anonymizedVisitors = 0;

        Visitor::query()
            ->whereDoesntHave('visits', function ($query) use ($cutoff): void {
                $query->whereNull('check_out_time')
                    ->orWhere('check_out_time', '>=', $cutoff);
            })
            ->with('user')
            ->chunkById(100, function ($visitors) use (&$anonymizedVisitors): void {
                foreach ($visitors as $visitor) {
                    $user = $visitor->user;

                    if (! $user) {
                        continue;
                    }

                    $user->forceFill([
                        'name' => 'Geanonimiseerde bezoeker',
                        'email' => sprintf('geanonimiseerd-bezoeker-%d@example.invalid', $user->id),
                        'password' => Str::random(64),
                        'email_verified_at' => null,
                        'remember_token' => null,
                    ])->save();

                    $visitor->forceFill([
                        'company_name' => null,
                    ])->save();

                    $anonymizedVisitors++;
                }
            });

        $this->info(sprintf('Anonymized %d visit(s) and %d visitor profile(s).', $updatedVisits, $anonymizedVisitors));

        return self::SUCCESS;
    }
}
