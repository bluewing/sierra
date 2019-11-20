# Bluewing Shared Server

Bluewing shared server contains the necessary base functionality to enable the underpinnings of Bluewing applications.

## User Instructions 

## Getting Started

### Prerequisites

### Installation

To install Bluewing Shared Server in a project, run:

```bash
composer required bluewing-shared-server
```

To install Bluewing Shared Server from Git:

```bash
git clone <url>
cd ./bluewing-shared-server
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

