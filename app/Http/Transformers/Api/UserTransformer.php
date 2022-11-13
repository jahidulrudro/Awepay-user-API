<?php

namespace App\Http\Transformers\Api;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\Paginator;

class UserTransformer
{
    /**
     *  @param mixed
     *  @return array
     */
    public function response($result)
    {
        if ($result instanceof Collection) {
            return $result->map(function($item) {
                return $this->toArray($item);
            });
        }

        if ($result instanceof Paginator) {
            return array_map(function($element) {
                return $this->toArray($element);
            },

            $result->items()
        );
        }

        return $this->toArray($result);
    }

    /**
     * @param App\Models\User
     * @return array
     */

    private function toArray(User $user): array
    {
        return [
            'id' => $user->id,
            'fullName' => $user->fullName,
            'email' => $user->email,
            'phone' => $user->phone,
            'age' => $user->age,
            'created_at' => $user->created_at->format('d/m/Y'),
            'updated_at' => $user->updated_at->format('d/m/Y'),
        ];
    }
}
