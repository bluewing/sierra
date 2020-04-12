<?php

namespace Bluewing\Services;

use Bluewing\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Support\Str;

class PreferencesBuilder {

    /**
     * Given an `Organization`, create all the necessary `Preference`'s for
     * the `Organization`.
     *
     * @param Model $organization - The `Organization` to create preferences for.
     *
     * @return void
     */
    public function populate(Model $organization): void {
        $now = Carbon::now();

        $preferenceTemplateModel = createModel(config('bluewing.preferences.preferenceTemplateModel'));

        $preferences = $preferenceTemplateModel->all()->map(function($item, $key) use($organization, $now) {
            return [
                'id'                    => Str::uuid()->toString(),
                'organizationId'        => $organization->id,
                'preferenceTemplateId'  => $item->id,
                'value'                 => $item->defaultValue,
                'createdAt'             => $now,
                'updatedAt'             => $now
            ];
        });

        $preferencesModel = createModel(config('bluewing.preferences.preferenceModel'));
        $preferencesModel->insert($preferences->toArray());
    }

    /**
     * Parses a given `Preference` value into the appropriate data type.
     *
     * @param Model $preference - The preference that should have its value parsed correctly.
     *
     * @return mixed - The parsed value in the correct data type.
     */
    public function parse(Model $preference) {
        switch ($preference->preferenceTemplate->type) {
            case 'boolean':
                return boolval($preference->value);

            case 'number':
                return intval($preference->value);

            case 'json':
                return json_decode($preference->value);

            case 'string':
            case 'file':
            default:
                return $preference->value;
        }
    }
}
