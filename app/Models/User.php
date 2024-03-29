<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use DateTime;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'premium_expiration_date'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public static function getAuthenticatedUser()
    {
        if (Auth::check()) {
            return Auth::user();
        }
        return null;
    }

    public function workspaces()
    {
        return $this
            ->belongsToMany(Workspace::class, 'link_between_users_and_workspaces')
            ->withPivot('is_PO');
    }

    public function created_workspaces()
    {
        return $this->hasMany(Workspace::class, 'created_by_id');
    }

    public function assignedTasks(): HasMany
    {
        return $this->hasMany(Task::class, 'assigned_to');
    }

    public function hasWorkspace(Workspace $workspace)
    {
        return $this->workspaces()->where('id', $workspace->id)->exists();
    }

    public function rewards()
    {
        return $this->hasMany(Reward::class);
    }

    public function level()
    {
        return $this->belongsTo(Level::class);
    }

    public function quests()
    {
        return $this->belongsToMany(Quest::class, 'users_quests')
            ->withPivot(['completed_count', 'in_progress', 'is_completed', 'date_completed']);
    }

    protected static function booted()
    {
        static::created(function (User $user) {
            // Obtenez tous les quests
            $user->initQuestsPivotData();
        });
    }

    public function initQuestsPivotData()
    {
        $quests = DB::table('quests')->get();
        foreach ($quests as $quest) {
            $this->quests()->attach($quest->id, [
                'completed_count' => 0,
                'in_progress' => $quest->level == 1,
                'is_completed' => false,
                'date_completed' => null,
            ]);
        }
        $this->save();
    }

    /**
     * @throws \Exception
     */
    public function isPremiumValid($forceConvertDate=false) {
        // Convert the premium expiration date to a DateTime object
        $premiumExpirationDate = $this->premium_expiration_date;

        if (!$premiumExpirationDate) {
            return false;
        }

        // Get today's date as a DateTime object
        $today = new DateTime();

        if($forceConvertDate){
            $premiumExpirationDate = new DateTime($premiumExpirationDate);
        }

        // Compare the two dates
        return $premiumExpirationDate >= $today;
    }

    public function linksBetweenUsersAndWorkspaces()
    {
        return $this->hasMany(LinkBetweenUsersAndWorkspaces::class, 'user_id');
    }
}
