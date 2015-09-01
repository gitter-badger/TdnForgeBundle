[![Dependency Status][version eye shield]][version eye]
[![GitHub issues][github issues]][issues page]
[![Total Downloads][downloads shield]][packagist page]
[![License][license shield]][packagist page]
[![Latest Stable Version][latest version shield]][packagist page]
[![Scrutinizer Code Quality][scrutinizer score shield]][scrutinizer page]
[![Travis][travis build shield]][travis page]
[![Coverage Status][coveralls badge]][coveralls page]
[![Codacy][codacy shield]][codacy page]
[![SensioLabsInsight][sensio shield]][sensio page]

![Icon][forge icon]

TdnForgeBundle
==============
A [Symfony 2][symfony 2] project/component generator.

Inspired by [JBoss Forge](http://forge.jboss.org/).

Description
-----------
TdnForgeBundle is a <b>very opinionated</b> bundle that can scaffold
an entire restful application or selected components, based on your doctrine entities.

##### Why opinionated?
While Symfony by design leaves a lot of options opens to developers this bundle makes quite a
few assumptions as to how your application should be generated (while providing minimal options).
It leverages a number of bundles that normally developers are free to not use in their application.

Required bundle:

- DoctrineBundle (required)

Optional bundles include:

- FOSRestBundle
- JMSSerializerBundle
- NelmioApiDocBundle
- SonataAdminBundle
- LiipFunctionalTestBundle

You are free to select which ones you want installed based on what you are trying to generate.

Read the documentation if you have any questions about a specific feature.

##### What can it generate?

First, all generated code meets the following criteria:

* Follows PSR2-4.
* Follows SF2 best practices (If it does not submit an issue and it will be fixed)
* Has minimal code duplication while still verifying types

The application provides the following generators (with some more in the works):

- Controller (Restful)
  - With optional swagger documentation
  - With optional functional tests
- Form types
- Rest Handlers (as services using yaml, xml, or annotations)
- Entity Managers (as services using yaml, xml, or annotations)
- Routing File
- Alice Fixtures (with optional static data which is recommended)
- Sonata Scaffolding
- Basic bootstrap twig templates

Generators can be used individually (see respective commands) or in sequence using the `forge:project` command.

See the [road map](#road-map) for overview of features in progress and planned for later versions.

[note]<sub>Some code duplication would be removed [if PHP 7.1 includes generics].</sub>

Documentation
-------------

For documentation, see:

    Resources/doc/

For a better documentation format please view [the documentation page].

For source API please checkout the [api docs].

Road Map
--------
![Under development][milestone shield]
- [x] REST Controllers
  - [x] API Documentation
  - [ ] Tests (requires existing [Doctrine Fixtures](/Doctrine/DoctrineFixturesBundle))
- [x] Form Types
- [x] Rest Handlers (as services)
- [x] Entity Managers (as services)
- [x] Routing
- [ ] Project generator: Scaffolds a project based on your entities (proxies all commands)
- [x] Support multiple formats (Yaml, annotations, xml) for generated service files
  - [x] Add option to use DiExtraBundle for generated code
- [ ] Generate Sonata admin
  - [ ] With basic sonata yaml configuraiton.
- [ ] Generate Alice Fixtures

![Planned][planned shield]
- [ ] Enable form events with `--events`
- [ ] Create a more RESTFUL interface for relationships e.g. `PUT /notes/1/label/2` 
  to create a relationship of one-to-many between notes and labels.
- [ ] Add option to implement symfony ACL and use @Secure and @PreAuthorize in controllers.
- [ ] Generate a working configuration between popular FOS UserBundle, RestBundle, and HWIOAuthBundle.
- [ ] Generate Entity Interfaces
- [ ] Generate entity serializer config (yaml, xml, annotations)
- [ ] Generate basic twig HTML (To be able to respond to xml, json and HTML)

Contributing
------------

If you are contributing or otherwise developing in this bundle, please read the [CONTRIBUTING](CONTRIBUTING.md) file
and the [contributing section] of the docs.

License
-------

This bundle is released under the MIT license. See the complete license in the
bundle:

    Resources/meta/LICENSE

Other questions
---------------

####Why is the `composer.lock` file commited?####
Both Scrutinizer-CI and SensioLabs Insight require the lock file to run (or it makes the process faster). 
Please check out this [scrutinizer documentation page] explaining how commiting composer.lock files does
 not tie any user of the library to any specific versions.


[if PHP 7.1 includes generics]: https://wiki.php.net/rfc/generics
[the documentation page]: https://thedevnetwork.github.io/TdnForgeBundle
[version eye shield]: https://www.versioneye.com/user/projects/55e409bfc6d8f200150003bd/badge.svg?style=flat-square
[version eye]: https://www.versioneye.com/user/projects/55e409bfc6d8f200150003bd
[github issues]: https://img.shields.io/github/issues/vpassapera/tdnforgebundle.svg?style=flat-square
[issues page]: https://github.com/vpassapera/TdnForgeBundle/issues
[downloads shield]: https://img.shields.io/packagist/dt/tdn/forgebundle.svg?style=flat-square
[packagist page]: https://packagist.org/packages/tdn/forgebundle
[license shield]: https://img.shields.io/packagist/l/tdn/forgebundle.svg?style=flat-square
[latest version shield]: https://img.shields.io/packagist/v/tdn/forgebundle.svg?style=flat-square
[scrutinizer score shield]: https://img.shields.io/scrutinizer/g/vpassapera/TdnForgeBundle.svg?style=flat-square
[scrutinizer page]: https://scrutinizer-ci.com/g/vpassapera/TdnForgeBundle
[scrutinizer documentation page]: https://scrutinizer-ci.com/docs/tools/php/php-analyzer/guides/composer_dependencies
[travis build shield]: https://img.shields.io/travis/vpassapera/TdnForgeBundle.svg?style=flat-square
[travis page]: https://travis-ci.org/vpassapera/TdnForgeBundle
[coveralls badge]: https://img.shields.io/coveralls/vpassapera/TdnForgeBundle/develop.svg?style=flat-square
[coveralls page]: https://coveralls.io/github/vpassapera/TdnForgeBundle?branch=develop
[codacy shield]: https://img.shields.io/codacy/66793ec4170a44e881a57719289ba787.svg?style=flat-square
[codacy page]: https://www.codacy.com/public/vpassapera/TdnForgeBundle
[sensio shield]: https://insight.sensiolabs.com/projects/06cbb1f1-948c-442d-97da-06836bb6068d/mini.png
[sensio page]: https://insight.sensiolabs.com/projects/06cbb1f1-948c-442d-97da-06836bb6068d
[forge icon]: https://raw.githubusercontent.com/TheDevNetwork/Aux/master/images/forge.png
[milestone shield]: https://img.shields.io/badge/milestone-1.0.0-green.svg
[symfony 2]: http://symfony.com
[note]: https://img.shields.io/badge/note-*-orange.svg
[planned shield]: https://img.shields.io/badge/status-planned-5F9FDE.svg
[contributing section]: https://thedevnetwork.github.io/TdnForgeBundle/_static/docs/contributing/index.html
[api docs]: https://thedevnetwork.github.io/TdnForgeBundle/_static/api/index.html
