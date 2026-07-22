# Release Checklist

Target: `v1.0.0`

Current status: Release candidate preparation.

## Completed Locally

- [x] PRD compliance reviewed.
- [x] README final drafted.
- [x] ERD final drafted.
- [x] Architecture documentation drafted.
- [x] Deployment guide drafted.
- [x] Security policy drafted.
- [x] Changelog drafted.
- [x] License added.
- [x] Demo credentials documented for local/demo use.
- [x] Production credentials documented as separate from demo credentials.
- [x] No production secret committed.

## Verification

- [x] Migration passes final release verification.
- [x] Seeder passes final release verification.
- [x] Full test suite passes final release verification.
- [x] Pint passes final release verification.
- [x] Frontend build passes final release verification.
- [ ] Composer audit passes.
- [x] Config cache passes.
- [x] Route cache passes.
- [x] View cache passes.
- [ ] CI green on GitHub Actions.

## Production Readiness

- [ ] `APP_DEBUG=false` confirmed in production.
- [ ] Production credentials configured outside Git.
- [ ] Deployment completed.
- [ ] Post-deployment smoke test completed.
- [ ] Database backup plan confirmed.
- [ ] Private attachment storage backup plan confirmed.

## Portfolio Assets

- [ ] Screenshots captured.
- [ ] Demo video recorded.
- [ ] Live deployment URL verified.

## Release Publishing

- [ ] Tag `v1.0.0` created.
- [ ] GitHub Release created.
