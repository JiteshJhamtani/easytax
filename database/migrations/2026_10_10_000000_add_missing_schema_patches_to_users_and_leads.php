<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Users table missing profile columns, soft deletes & dynamic role length
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'mobile_number')) {
                $table->string('mobile_number', 20)->nullable();
            }
            if (! Schema::hasColumn('users', 'whatsapp_no')) {
                $table->string('whatsapp_no', 20)->nullable();
            }
            if (! Schema::hasColumn('users', 'address')) {
                $table->text('address')->nullable();
            }
            if (! Schema::hasColumn('users', 'deleted_at')) {
                $table->softDeletes();
            }
        });

        $driver = DB::getDriverName();
        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY COLUMN role VARCHAR(50) NOT NULL DEFAULT 'AGENT'");
        }

        // 2. Leads table amount column
        Schema::table('leads', function (Blueprint $table) {
            if (! Schema::hasColumn('leads', 'amount')) {
                $table->decimal('amount', 10, 2)->nullable();
            }
        });

        // 3. Services table sort_order and mapping columns
        Schema::table('services', function (Blueprint $table) {
            if (! Schema::hasColumn('services', 'sort_order')) {
                $table->integer('sort_order')->default(0);
            }
            if (! Schema::hasColumn('services', 'primary_data_field')) {
                $table->string('primary_data_field')->nullable();
            }
            if (! Schema::hasColumn('services', 'whatsapp_number_field')) {
                $table->string('whatsapp_number_field')->nullable();
            }
            if (! Schema::hasColumn('services', 'applicant_email_field')) {
                $table->string('applicant_email_field')->nullable();
            }
        });

        // 4. Service pricing rules table
        if (! Schema::hasTable('service_pricing_rules')) {
            Schema::create('service_pricing_rules', function (Blueprint $table) {
                $table->id();
                $table->foreignId('service_id')->constrained()->onDelete('cascade');
                $table->string('gst_type')->nullable();
                $table->string('turnover')->nullable();
                $table->string('frequency')->nullable();
                $table->string('plan')->nullable();
                $table->decimal('base_price', 10, 2)->default(0);
                $table->decimal('commission_amount', 10, 2)->default(0);
                $table->string('itr_type')->nullable();
                $table->string('itr_salary')->nullable();
                $table->string('itr_business')->nullable();
                $table->string('itr_capital_gains')->nullable();
                $table->string('itr_50l')->nullable();
                $table->string('user_type')->nullable();
                $table->timestamps();
            });
        }

        // 5. Applications table assigned_to, pending_reason, coupon_id, coupon_bonus
        Schema::table('applications', function (Blueprint $table) {
            if (! Schema::hasColumn('applications', 'assigned_to')) {
                $table->unsignedBigInteger('assigned_to')->nullable()->after('agent_id');
                $table->index('assigned_to');
            }
            if (! Schema::hasColumn('applications', 'coupon_id')) {
                $table->unsignedBigInteger('coupon_id')->nullable()->after('agent_id');
                $table->index('coupon_id');
            }
            if (! Schema::hasColumn('applications', 'coupon_bonus')) {
                $table->decimal('coupon_bonus', 10, 2)->default(0.00)->after('commission_amount');
            }
            if (! Schema::hasColumn('applications', 'pending_reason')) {
                $table->text('pending_reason')->nullable()->after('status');
            }
        });

        // 6. Operator service rates table
        if (! Schema::hasTable('operator_service_rates')) {
            Schema::create('operator_service_rates', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('operator_id');
                $table->unsignedBigInteger('service_id');
                $table->decimal('price', 10, 2)->default(0.00);
                $table->timestamps();

                $table->unique(['operator_id', 'service_id']);
                $table->index('operator_id');
            });
        }

        // 7. Operator payouts table
        if (! Schema::hasTable('operator_payouts')) {
            Schema::create('operator_payouts', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('operator_id');
                $table->decimal('amount', 10, 2);
                $table->text('payment_note')->nullable();
                $table->timestamp('paid_at')->useCurrent();
                $table->timestamps();

                $table->index('operator_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('operator_payouts')) {
            Schema::dropIfExists('operator_payouts');
        }

        if (Schema::hasTable('operator_service_rates')) {
            Schema::dropIfExists('operator_service_rates');
        }

        if (Schema::hasTable('service_pricing_rules')) {
            Schema::dropIfExists('service_pricing_rules');
        }

        Schema::table('applications', function (Blueprint $table) {
            if (Schema::hasColumn('applications', 'pending_reason')) {
                $table->dropColumn('pending_reason');
            }
            if (Schema::hasColumn('applications', 'coupon_bonus')) {
                $table->dropColumn('coupon_bonus');
            }
            if (Schema::hasColumn('applications', 'coupon_id')) {
                $table->dropIndex(['coupon_id']);
                $table->dropColumn('coupon_id');
            }
            if (Schema::hasColumn('applications', 'assigned_to')) {
                $table->dropIndex(['assigned_to']);
                $table->dropColumn('assigned_to');
            }
        });

        Schema::table('services', function (Blueprint $table) {
            if (Schema::hasColumn('services', 'applicant_email_field')) {
                $table->dropColumn('applicant_email_field');
            }
            if (Schema::hasColumn('services', 'whatsapp_number_field')) {
                $table->dropColumn('whatsapp_number_field');
            }
            if (Schema::hasColumn('services', 'primary_data_field')) {
                $table->dropColumn('primary_data_field');
            }
            if (Schema::hasColumn('services', 'sort_order')) {
                $table->dropColumn('sort_order');
            }
        });

        Schema::table('leads', function (Blueprint $table) {
            if (Schema::hasColumn('leads', 'amount')) {
                $table->dropColumn('amount');
            }
        });

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'deleted_at')) {
                $table->dropSoftDeletes();
            }
            if (Schema::hasColumn('users', 'address')) {
                $table->dropColumn('address');
            }
            if (Schema::hasColumn('users', 'whatsapp_no')) {
                $table->dropColumn('whatsapp_no');
            }
            if (Schema::hasColumn('users', 'mobile_number')) {
                $table->dropColumn('mobile_number');
            }
        });
    }
};
