iomad-tool_companydomain
========================

[![Moodle Plugin CI](https://github.com/lernlink/iomad-tool_companydomain/workflows/Moodle%20Plugin%20CI/badge.svg?branch=master)](https://github.com/lernlink/iomad-tool_companydomain/actions?query=workflow%3A%22Moodle+Plugin+CI%22+branch%3Amaster)

IOMAD Plugin which adds manually or automatically created users to companies based on the email address domain.


Requirements
------------

This plugin requires IOMAD 3.9+


Motivation for this plugin
--------------------------

Within a company in IOMAD, you can set a list of company domains. As soon as a user signs up for an IOMAD account, he is added to this company based on his email address domain. Furthermore, some profile settings (like forum tracking and the theme) are set for this user. Finally, the user is auto-enrolled to the courses of the company.

Unfortunaly, this mechanism only works for self-signups. If an IOMAD account is created manually by the administrator or is created automatically within the first LDAP login of a user, the user is not added to any company.

This plugin overcomes this shortcoming by listening to the user_created event and will then take the same actions just like if the user had signed up himself.
Additionally, this plugin provides a scheduled task which will regularly analyse all existing IOMAD users if they are a member of a company and, if not, will add them to the company matching their email address domain(s).


Installation
------------

Install the plugin like any other plugin to folder
/admin/tool/companydomain

See http://docs.moodle.org/en/Installing_plugins for details on installing Moodle plugins


Usage & Settings
----------------

After installing the plugin, it is ready to use without the need for any configuration.


Limitations
-----------

This plugin currently has two limitations which administrators should be aware of:

1. The plugin does not deal with company switches. If a user is a member of a company, the plugin does not touch him anymore even if he changes his email address to a domain which matches another company. This is the same behaviour as IOMAD core behaves for self-signup users. In these cases, the administrator will have to do the necessary switches manually.

2. The plugin does not deal with email address domains which have been added to multiple companies. The IOMAD GUI does not prevent administrators from adding a domain to multiple companies, however the GUI is not really prepared that a user is a member of more than one company. In these cases, the plugin will simply ignore users with this email address domain and will not add them to any company until the domain is removed from all companies but one.


Theme support
-------------

This plugin acts behind the scenes, therefore it should work with all Moodle themes.
This plugin is developed and tested on Moodle Core's Boost theme.
It should also work with Boost child themes, including Moodle Core's Classic theme. However, we can't support any other theme than Boost.


Plugin repositories
-------------------

This plugin is not published in the Moodle plugins repository.

The latest development version can be found on Github:
https://github.com/lernlink/iomad-tool_companydomain


Bug and problem reports
-----------------------

This plugin is carefully developed and thoroughly tested, but bugs and problems can always appear.

Please report bugs and problems on Github:
https://github.com/lernlink/iomad-tool_companydomain/issues


Feature proposals
-----------------

Due to limited resources, the functionality of this plugin is primarily implemented for our own local needs and published as-is to the community. We are aware that members of the community will have other needs and would love to see them solved by this plugin.

Please issue feature proposals on Github:
https://github.com/lernlink/iomad-tool_companydomain/issues

Please create pull requests on Github:
https://github.com/lernlink/iomad-tool_companydomain/pulls


Paid support
------------

We are always interested to read about your issues and feature proposals or even get a pull request from you on Github. However, please note that our time for working on community Github issues is limited.

As certified Moodle Partner, we also offer paid support for this plugin. If you are interested, please have a look at our services on https://lern.link or get in touch with us directly via team@lernlink.de.


Moodle release support
----------------------

This plugin is only maintained for the most recent major release of Moodle as well as the most recent LTS release of Moodle. Bugfixes are backported to the LTS release. However, new features and improvements are not necessarily backported to the LTS release.

Apart from these maintained releases, previous versions of this plugin which work in legacy major releases of Moodle are still available as-is without any further updates in the Moodle Plugins repository.

There may be several weeks after a new major release of Moodle has been published until we can do a compatibility check and fix problems if necessary. If you encounter problems with a new major release of Moodle - or can confirm that this plugin still works with a new major release - please let us know on Github.

If you are running a legacy version of Moodle, but want or need to run the latest version of this plugin, you can get the latest version of the plugin, remove the line starting with $plugin->requires from version.php and use this latest plugin version then on your legacy Moodle. However, please note that you will run this setup completely at your own risk. We can't support this approach in any way and there is an undeniable risk for erratic behavior.


Translating this plugin
-----------------------

This plugin does not contain any strings which are visible to a Moodle student / teacher and it can't be translated on AMOS as it is not published in the Moodle plugins repository. In our point of view, translating this plugin is not necessary.


Right-to-left support
---------------------

This plugin has not been tested with Moodle's support for right-to-left (RTL) languages.
If you want to use this plugin with a RTL language and it doesn't work as-is, you are free to send us a pull request on Github with modifications.


Maintainers
-----------

lern.link GmbH\
Alexander Bias


Copyright
---------

lern.link GmbH\
Alexander Bias
