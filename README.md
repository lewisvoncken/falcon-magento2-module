# Deity MagentoApi

Api adaptor for Deity Falcon

## Getting Started

Install and configure magento shop to proceed with Deity Adaptor installation

### Prerequisites

Deity Magento module is compatible with magento version 2.2. CE and EE versions. 
Full scale support of versions 2.0.* and 2.1.* is not our priority.
However if you encounter an issue running with magento version lower than 2.2 feel free
to open an issue or reach out to our support channel.
```
magento > 2.2
```

### Installing

Installing deity magento module is similar to installing any module for Magento 2 platform 

```
composer require deity/falcon-magento 1.0.2
bin/magento setup:upgrade
```
When the module is setup, create one extra magento admin user for Deity Falcon to connect

```
bin/magento  admin:user:create  --admin-user='your-admin-username' --admin-password='your-admin-password' --admin-email='admin@deity.test' --admin-firstname='node' --admin-lastname='Deity'
```

## Documentation

Check out integration documentation for the module. 
[Integration_notes.md](docs/Integration_notes.md)

## Versioning

We use [SemVer](http://semver.org/) for versioning. For the versions available, see the [tags on this repository](https://github.com/deity-io/falcon-magento2-module/tags). 

## License

This project is licensed under Open Software License ("OSL") v. 3.0 - see the [LICENSE.md](LICENSE.md) file for details
