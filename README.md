[![Dependency Status][version eye shield]][version eye]
[![GitHub issues][github issues]][issues page]
[![Total Downloads][downloads shield]][packagist page]
[![License][license shield]][packagist page]
[![Latest Stable Version][latest version shield]][packagist page]
[![Scrutinizer Code Quality][scrutinizer score shield]][scrutinizer page]
[![Scrutinizer Code Coverage][scrutinizer coverage shield]][scrutinizer page]
[![Travis][travis build shield]][travis page]
[![Codacy][codacy shield]][codacy page]
[![SensioLabsInsight][sensio shield]][sensio page]

![Icon][forge icon]

TdnForgeBundle
==============
A [Symfony 2][symfony 2] project generator (scaffolding).

Inspired by [JBoss Forge](http://forge.jboss.org/).

Description
-----------
TdnForgeBundle is a <b>very opinionated</b> bundle that scaffolds
a restful application (or selected parts) from your doctrine entities.

##### Why opinionated?
While Symfony by design leaves a lot of options opens to developers this bundle makes quite a
few assumptions as to how your application should be generated. It includes a number of bundles
that normally developers are free to not use in their application (or use an alternative).

##### Generated code features:
* Create working application from entities
* Create tests for controllers
* CRUD application that follows PSR-4
* Generated code follows SF2 Best practices
* Full auto-completion (with minimal code duplication if PHP 7.1 includes generics).
* Auto-generated Functional tests for controllers.
* Auto-generated API documentation.

See the [road map](#road-map) for overview of features in progress and planned for later versions.

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

![Planned][planned shield]
- [ ] Enable form events with `--events`
- [ ] Create a more RESTFUL interface for relationships e.g. `PUT /notes/1/label/2` 
  to create a relationship of one-to-many between notes and labels.
- [ ] Add option to implement symfony ACL and use @Secure and @PreAuthorize in controllers.
- [ ] Generate a working configuration between popular FOS UserBundle, RestBundle, and HWIOAuthBundle.
- [ ] Generate Entity Interfaces
- [ ] Generate initial serializer annotations
- [ ] Generate basic twig HTML (To be able to respond to xml, json and HTML)

Applications using TdnForgeBundle
---------------------------------
[Project Ilios][ilios project]

<sub>A leading Curriculum Management System specializing
 in the health professions educational community. [browse source][ilios core bundle]</sub>

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
Please check out this [scrutinizer documentation page] explaining how commiting composer.lock files does not tie any user of the 
library to any specific versions.

[the documentation page]: https://thedevnetwork.github.io/TdnForgeBundle
[version eye shield]: https://www.versioneye.com/user/projects/54f6e619dd0a3627be000052/badge.svg?style=flat-square
[version eye]: https://www.versioneye.com/user/projects/54f6e619dd0a3627be000052
[github issues]: https://img.shields.io/github/issues/thedevnetwork/tdnforgebundle.svg?style=flat-square
[issues page]: https://github.com/thedevnetwork/TdnForgeBundle/issues
[downloads shield]: https://img.shields.io/packagist/dt/tdn/forgebundle.svg?style=flat-square
[packagist page]: https://packagist.org/packages/tdn/forgebundle
[license shield]: https://img.shields.io/packagist/l/tdn/forgebundle.svg?style=flat-square
[latest version shield]: https://img.shields.io/packagist/v/tdn/forgebundle.svg?style=flat-square
[scrutinizer score shield]: https://img.shields.io/scrutinizer/g/TheDevNetwork/TdnForgeBundle.svg?style=flat-square
[scrutinizer page]: https://scrutinizer-ci.com/g/TheDevNetwork/TdnForgeBundle
[scrutinizer documentation page]: https://scrutinizer-ci.com/docs/tools/php/php-analyzer/guides/composer_dependencies
[scrutinizer coverage shield]: https://img.shields.io/scrutinizer/coverage/g/TheDevNetwork/TdnForgeBundle.svg?style=flat-square
[travis build shield]: https://img.shields.io/travis/TheDevNetwork/TdnForgeBundle.svg?style=flat-square
[travis page]: https://travis-ci.org/TheDevNetwork/TdnForgeBundle
[codacy shield]: https://img.shields.io/codacy/9a9be3063c8d44ca8709497469e3d097.svg?style=flat-square
[codacy page]: https://www.codacy.com/public/vpassapera/TdnForgeBundle_2
[sensio shield]: https://insight.sensiolabs.com/projects/84a6a21c-83e0-4f21-a66f-838d1ddc5e07/mini.png
[sensio page]: https://insight.sensiolabs.com/projects/84a6a21c-83e0-4f21-a66f-838d1ddc5e07
[forge icon]: https://raw.githubusercontent.com/TheDevNetwork/Aux/master/images/forge.png
[milestone shield]: https://img.shields.io/badge/milestone-1.0.0-green.svg
[symfony 2]: http://symfony.com
[note]: https://img.shields.io/badge/note-*-orange.svg
[planned shield]: https://img.shields.io/badge/status-planned-5F9FDE.svg
[ilios core bundle]: https://github.com/ilios/ilios/tree/master/src/Ilios/CoreBundle
[ilios project]: https://github.com/ilios/ilios
[contributing section]: https://thedevnetwork.github.io/TdnForgeBundle/_static/docs/contributing/index.html
[api docs]: https://thedevnetwork.github.io/TdnForgeBundle/_static/api/index.html
