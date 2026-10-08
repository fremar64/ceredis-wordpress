# Security

Production code is immutable and deployed from Git.

Do not:

- commit secrets;
- enable the WordPress file editor;
- install arbitrary plugins in production;
- enable automatic plugin/theme updates;
- reuse legacy production credentials;
- expose MySQL publicly.

All production secrets are managed by Coolify.

Security updates are handled by updating the pinned base image and rebuilding/redeploying the application.

The staging environment must be tested before production updates.
