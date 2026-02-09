<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;
class Garage extends Authenticatable
{
    use HasFactory;
    // use SoftDeletes;
    use Notifiable;
    protected $table = 'garages';
    protected $fillable = [
        'garage_name',
        'garage_company_number',
        'garage_vat_number',
        'garage_eori_number',
        'garage_phone',
        'garage_mobile',
        'garage_email',
        'password',
        'garage_street',
        'garage_city',
        'garage_zone',
        'garage_country',
        'garage_description',
        'garage_garage_opening_time',
        'garage_logo',
        'garage_banner',
        'garage_favicon',
        'garage_social_facebook',
        'garage_social_instagram',
        'garage_social_twitter',
        'garage_social_youtube',
        'garage_google_map_link',
        'garage_longitude',
        'garage_latitude',
        'garage_google_reviews_link',
        'garage_google_reviews_stars',
        'garage_google_reviews_count',
        'garage_website_url',
        'garage_notes',
        'garage_status',
        'garage_order_types',
        'garage_bank_name',
        'garage_bank_sort_code',
        'garage_account_number',
        'garage_revolut_source_id',
        'garage_revoult_counterparty_id',
        'commission_type',
        'commission_price',
        'card_processing_fee',
    ];


    protected $hidden = [
        'password',
        'remember_token',
    ];
    protected $guarded = ['_token', 'id', 'created_at', 'updated_at', 'deleted_at'];
    protected $dates = ['deleted_at'];

    public function vehicleDetails()
    {
        return $this->hasMany(VehicleDetail::class);
    }
    public function vehicles()
    {
        return $this->belongsToMany(VehicleDetail::class, 'customer_vehicle')->withTimestamps();
    }
    public function getEmailForPasswordReset()
    {
        return $this->customer_email;
    }
    public function reviews()
    {
        return $this->hasMany(GarageReview::class, 'garage_id');
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'garage_id');
    }

    public function Workshop()
    {
        return $this->hasMany(Workshop::class, 'garage_id');
    }
    /**
     * Send the password reset notification.
     *
     * @param string $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new CustomerResetPasswordNotification($token));
    }
    public function services()
    {
        return $this->hasMany(CarService::class, 'garage_id');
    }
    public function suppliers()
    {
        return $this->belongsToMany(Supplier::class, 'garage_supplier')
            ->withPivot([
                'import_method',
                'api_order_enable',
                'api_order_details',
                'file_path',
                'ftp_host',
                'ftp_user',
                'ftp_password',
                'ftp_directory',
                'status'
            ])
            ->withTimestamps();
    }

    /**
     * All payout records for this garage (from garage_payouts table)
     */
    public function payouts(): HasMany
    {
        return $this->hasMany(GaragePayout::class);
    }

    public function pendingPayouts(): HasMany
    {
        return $this->payouts()->where('status', 'pending');
    }

    public function completedPayouts(): HasMany
    {
        return $this->payouts()->where('status', 'completed');
    }

    public function getPendingPayoutTotalAttribute(): float
    {
        return $this->pendingPayouts()->sum('payout_amount');
    }

    public function getTotalPaidAttribute(): float
    {
        return $this->completedPayouts()->sum('payout_amount');
    }

    public function getTotalCommissionEarnedAttribute(): float
    {
        return $this->completedPayouts()->sum('platform_commission');
    }

    public function getPendingCountAttribute(): int
    {
        return $this->pendingPayouts()->count();
    }

    public function hasValidBankDetails(): bool
    {
        return !empty($this->account_holder_name) &&
            !empty($this->iban) &&
            (!empty($this->bic) || (!empty($this->sort_code) && !empty($this->account_number)));
    }

    public function hasRevolutCounterparty(): bool
    {
        return !empty($this->revolut_counterparty_id);
    }

    public function getCommissionRate(): float
    {
        return $this->commission_rate;
    }

    public function calculatePayoutAmount(float $workshopTotal): float
    {
        $commission = $workshopTotal * ($this->getCommissionRate() / 100);
        return $workshopTotal - $commission;
    }

    public function scopeWithPendingPayouts($query)
    {
        return $query->whereHas('payouts', fn($q) => $q->where('status', 'pending'));
    }

    public function getCommissionAmount(Workshop $workshop): float
    {
        $total = $workshop->grandTotal ?? 0;
        $quantity = $this->getWorkshopItemQuantity($workshop);

        switch ($this->commission_type) {
            case 'Percentage':
                return round($total * ($this->commission_price / 100), 2);

            case 'Fixed':
                if ($quantity > 0) {
                    return round($this->commission_price * $quantity, 2);
                }
                return (float) $this->commission_price;

            default:
                return 0.0;
        }
    }

    protected function getWorkshopItemQuantity(Workshop $workshop): int
    {
        if (method_exists($workshop, 'items')) {
            return $workshop->items()->count();
        }
        return 1;
    }

}
