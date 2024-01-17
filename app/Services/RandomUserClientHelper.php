<?php

namespace App\Services;

use Illuminate\Support\Collection;

class RandomUserClientHelper
{
    private string $field;
    private string $sortOrder;

    /**
     * Preparing Random Users data
     *
     * @param array $usersArray
     * @param string $field
     * @param string $sortOrder
     * @return array
     */
    public function processUsers(array $usersArray, string $field, string $sortOrder): array
    {
        $this->field = $field;
        $this->sortOrder = $sortOrder;

        $collection = collect($usersArray);
        $sortedCollection = $this->sortCollection($collection);
        $resultCollection = $this->collectionToOutput($sortedCollection);

        return array_values($resultCollection->all());
    }

    /**
     * Sorting collection
     *
     * @param Collection $collection
     * @return Collection
     */
    private function sortCollection(Collection $collection): Collection
    {
        $field = $this->field;
        $sortOrder = $this->sortOrder;

        $compareFunction = function ($a, $b) use ($field, $sortOrder) {
            $valueA = match ($field) {
                'last' => data_get($a, 'name.last'),
                'country' => data_get($a, 'location.country'),
                default => data_get($a, $field)
            };

            $valueB = match ($field) {
                'last' => data_get($b, 'name.last'),
                'country' => data_get($b, 'location.country'),
                default => data_get($b, $field)
            };

            $result = strcoll($valueA, $valueB);

            return ($sortOrder === 'asc') ? $result : -$result;
        };

        return $collection->sort($compareFunction);
    }

    /**
     * Prepare Random User collection to output
     *
     * @param Collection $sortedCollection
     * @return Collection
     */
    private function collectionToOutput(Collection $sortedCollection): Collection
    {
        return $sortedCollection->map(function ($user) {
            $first = $user['name']['first'] ?? '';
            $last = $user['name']['last'] ?? '';

            $userData = [];
            if ($first || $last) {
                $userData['full_name'] = trim($first . ' ' . $last);
            }
            if (isset($user['phone'])) {
                $userData['phone'] = $user['phone'];
            }
            if (isset($user['email'])) {
                $userData['email'] = $user['email'];
            }
            if (isset($user['location']['country'])) {
                $userData['country'] = data_get($user, 'location.country');
            }

            return $userData;
        });
    }
}
