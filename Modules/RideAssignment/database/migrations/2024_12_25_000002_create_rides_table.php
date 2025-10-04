<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\RideAssignment\app\Models\RideAssign;
use Modules\Subscription\app\Models\Location;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rides', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ride_assign_id')->constrained((new RideAssign())->getTable());
            $table->foreignId('driver_id')->constrained((new User())->getTable());
            $table->foreignId('parent_id')->constrained((new User())->getTable());
            $table->foreignId('pickup_location_id')->constrained((new Location())->getTable());
            $table->foreignId('dropoff_location_id')->constrained((new Location())->getTable());
            $table->enum('ride_type', ['pickup', 'return_home'])->default('pickup');
            $table->decimal('commission', 10, 2)->default(0);
            $table->date('date')->nullable();
            $table->time('pickup')->nullable();
            $table->time('drop_off')->nullable();
            $table->dateTime('end_at')->nullable();
            $table->string('face_verification1')->nullable();
            $table->string('selfie')->nullable();
            $table->string('face_verification2')->nullable();
            $table->string('end_pic')->nullable();
            $table->enum('status', [
                'assigned',
                'pending',
                'going_to_pickup',
                'arrived_at_pickup',
                'in_progress',
                'completed',
                'cancelled'
            ])->default('assigned');
            $table->timestamps();
            $table->softDeletes();
        });

        // 'assigned','in_progress','arrive_home','start_ride','completed','cancelled'
    }

    public function down(): void
    {
        Schema::dropIfExists('rides');
    }
};
