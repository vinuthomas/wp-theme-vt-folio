---
name: package-theme
description: Bump the theme version and build a fresh vinu-thomas-theme.zip on ~/Desktop for WordPress upload
disable-model-invocation: true
---

# Package theme for upload

Follow the exact recipe in `CLAUDE.md` under "Packaging the theme for upload". Do not skip steps or improvise a different flow — WordPress matches themes by the folder name inside the zip, so the internal folder must stay `vinu-thomas/`.

1. Read `style.css` line 7 (`Version: x.x.x`) to find the current version.
2. Determine the new version:
   - If the user gave a version or bump type (patch/minor) in the skill arguments, use it.
   - Otherwise ask: is this a patch (bug fix) or minor (new feature) bump?
   - Patch: `1.2.1 → 1.2.2`. Minor: `1.2.x → 1.3.0`.
3. Edit `style.css` line 7 to the new version.
4. Run, in order:
   ```bash
   rm -f ~/Desktop/vinu-thomas-theme.zip
   cp -r /Users/vinuthomas/code/wordpress_theme /tmp/vinu-thomas
   rm -f /tmp/vinu-thomas/CLAUDE.md
   rm -rf /tmp/vinu-thomas/.claude
   rm -rf /tmp/vinu-thomas/.git
   rm -rf /tmp/vinu-thomas/security-reports
   cd /tmp && zip -r ~/Desktop/vinu-thomas-theme.zip vinu-thomas --exclude "*/.DS_Store"
   rm -rf /tmp/vinu-thomas
   ```
5. Verify: `unzip -l ~/Desktop/vinu-thomas-theme.zip | grep style.css` must print `vinu-thomas/style.css`.
6. Report the final zip path and version to the user. Do not create a git commit, tag, or GitHub release as part of this skill — those are separate, explicit asks.
