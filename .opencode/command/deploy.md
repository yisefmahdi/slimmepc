---
description: Deploy the current push to the Hostinger server (auto via cron, or manual SSH).
agent: build
---

Deploy the code on `main` to the Hostinger production server for slimmepc.
Follow the `deploy` skill for details and troubleshooting.

1. Ensure work is committed and pushed:
   - `git status`, then commit if needed.
   - `git push origin main`.
2. Automatic: the server cron pulls and deploys within ~5 minutes. If the
   user wants the deploy immediately, run the manual deploy:
   - `ssh slimmepc "cd /domains/slimmepc.kulshy.online/public_html && bash scripts/deploy.sh"`
3. Verify by tailing the deploy log:
   - `ssh slimmepc "tail -n 20 /domains/slimmepc.kulshy.online/public_html/storage/logs/deploy.log"`

If an SSH password is required (no key authorized), read the local gitignored
file `deploy-credentials.local.md` — never commit the password.