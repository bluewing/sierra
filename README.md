# Sierra

Sierra contains the necessary base functionality to enable the underpinnings of Bluewing applications.

## User Instructions 

## Getting Started

### Prerequisites

### Installation

To install Sierra in a project, run:

```bash
composer require bluewing/sierra
```

To install Sierra from Git:

```bash
git clone https://github.com/bluewing/sierra
cd ./sierra
```

### Building

## Tests

## Deployment

## Built With

## Contributing

## Versioning

## Author

## Notes

Some todos:

* Migrate shared models such as `ApiEndpoint`, `RefreshToken`, `Preference`, & `PreferenceTemplate` into package.
* Migrate shared migrations into package (`ApiEndpoints`, `RefreshTokens`, `Preferences`, PreferenceTemplates`).
* Migrate shared controllers into package.
* Migrate shared seeders into package.
* All Service Providers should be imported into child package on `php artisan vendor:publish`.

## License

