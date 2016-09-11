# Contributing to Lobby
> Hi there! Interested in contributing to Lobby? We'd love your help. Lobby is an open source project, built one contribution at a time by users like you.

## Where to get help or report a problem
* If you think you've found a bug within a Lobby plugin, open an issue in that plugin's repository.
* If you think you've found a bug within Lobby itself, [open an issue](https://github.com/lobby/lobby/issues/new).
* If you think you've found a bug within Lobby itself, [talk with the maintainers](https://gitter.im/subins2000/lobby).

## Ways to contribute
Whether you're a developer, a designer, or just a Lobby devotee, there are lots of ways to contribute. Here's a few ideas:
* [Install Lobby on your computer](https://lobby.subinsb.com/download) and kick the tires. Does it work? Does it do what you'd expect? If not, [open an issue](https://github.com/lobby/lobby/issues/new) and let us know.
* Comment on some of the project's [open issues](https://github.com/lobby/lobby/issues). Have you experienced the same problem? Know a work around? Do you have a suggestion for how the feature could be better?
* Find [an open issue](https://github.com/lobby/lobby/issues) (especially [those labeled `help-wanted`](https://github.com/lobby/lobby/issues?q=is%3Aopen+is%3Aissue+label%3Ahelp-wanted)), and submit a proposed fix. If it's your first pull request, we promise we won't bite, and are glad to answer any questions.
* Help evaluate [open pull requests](https://github.com/lobby/lobby/pulls), by testing the changes locally and reviewing what's proposed.

## Submitting a pull request
### Pull requests generally
* The smaller the proposed change, the better. If you'd like to propose two unrelated changes, submit two pull requests.
* The more information, the better. Make judicious use of the pull request body. Describe what changes were made, why you made them, and what impact they will have for users.
* Pull request are easy and fun. If this is your first pull request, it may help to [understand GitHub Flow](https://guides.github.com/introduction/flow/).

### Submitting a pull request via github.com
Many small changes can be made entirely through the github.com web interface.
1. Navigate to the file within [`lobby/lobby`](https://github.com/lobby/lobby) that you'd like to edit.
2. Click the pencil icon in the top right corner to edit the file
3. Make your proposed changes
4. Click "Propose file change"
5. Click "Create pull request"
6. Add a descriptive title and detailed description for your proposed change. The more information the better.
7. Click "Create pull request"

That's it! You'll be automatically subscribed to receive updates as others review your proposed change and provide feedback.

### Submitting a pull request via Git command line
1. Fork the project by clicking "Fork" in the top right corner of [`lobby/lobby`](https://github.com/lobby/lobby).
2. Clone the repository locally `git clone https://github.com/<you-username>/lobby`.
3. Create a new, descriptively named branch to contain your change ( `git checkout -b my-awesome-feature` ).
4. Hack away, add tests. Not necessarily in that order.
5. Make sure everything still passes by running `script/cibuild` (see [the tests section](#running-tests-locally) below)
6. Push the branch up ( `git push origin my-awesome-feature` ).
7. Create a pull request by visiting `https://github.com/<your-username>/lobby` and following the instructions at the top of the screen.

## Proposing updates to the documentation
We want the Lobby documentation to be the best it can be. We've open-sourced our docs and we welcome any pull requests if you find it lacking.

### How to submit changes
All pull requests should be directed at the `master` branch (the default branch).

## Code Contributions
Interesting in submitting a pull request? Awesome. Read on. There's a few common gotchas that we'd love to help you avoid.

### Tests and documentation
Any time you propose a code change, you should also include updates to the documentation and tests within the same pull request.

#### Tests
* See [Travis CI builds](https://travis-ci.org/LobbyOS/lobby).

## A thank you

Thanks! Hacking on Lobby should be fun. If you find any of this hard to figure out, let us know so we can improve our process or documentation!
