Contributing
------------
Thank you for taking the time to read this document. Pull requests are always welcomed and appreciated.

TdnPilotBundle is an open source project that uses the [MIT](http://opensource.org/licenses/MIT) license.

The key words "MUST", "MUST NOT", "REQUIRED", "SHALL", "SHALL NOT", "SHOULD",
"SHOULD NOT", "RECOMMENDED", "MAY", and "OPTIONAL" in this document are to be
interpreted as described in [RFC 2119].

Contributors should keep in mind the following rules when creating pull requests:

  * You MUST rebase your PR against the branch you forked from (do this frequently)
   and squash your commit. See the [apache git usage] document which explains the why
   and this [tutorial] which explains the how. Extra information about rebasing/reflog
   can be located in the [rebase documentation] and [reflog documentation] respectively

  * You MUST follow [PSR-1] and [PSR-2]

  * You SHOULD [run the local checks]

  * You MUST write/update unit tests accordingly

  * You MUST write a description which gives context to the PR

  * You SHOULD write/update documentation accordingly

  * The following checks will be automatically performed on PRs:
     - Code Style (PSR-1, PSR-2)
     - Scrutinizer checks
     - PhpUnit tests

If any of those fail the PR will not be merged until it is updated accordingly.

If you're simply adding your application to the README.md file, the commit will build accordingly.

Thank you for any and all contributions or simply using the bundle, +1 internet to you!

![+1 Internet][one free internet]

[one free internet]: https://raw.githubusercontent.com/TheDevNetwork/Aux/master/images/OneFreeInternet.png
[run the local checks]: https://github.com/TheDevNetwork/TdnPilotBundle/blob/master/Resources/doc/running-checks.md
[apache git usage]: https://cwiki.apache.org/confluence/display/FLEX/Good+vs+Bad+Git+usage
[tutorial]: http://gitready.com/advanced/2009/02/10/squashing-commits-with-rebase.html
[reflog documentation]: https://www.atlassian.com/git/tutorials/rewriting-history/git-reflog
[rebase documentation]: http://git-scm.com/book/en/v2/Git-Branching-Rebasing
[RFC 2119]: http://www.ietf.org/rfc/rfc2119.txt
[PSR-1]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-1-basic-coding-standard.md
[PSR-2]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md
