# Instructor notes

## Intended student flow

1. Access the SEED attacker machine.
2. Browse to the Elgg application.
3. Log in as Samy and identify a stored-XSS input.
4. Use Alice as the victim for browser-side execution.
5. Read browser-readable cookies.
6. Demonstrate an HTTP request carrying cookie data.
7. Observe and reproduce Elgg's friend-add request.
8. Observe and reproduce the profile-update POST.
9. Turn the attack into a DOM-based self-propagating worm.
10. Complete the CSP experiment by editing the server configuration.

## Why APG is used

The important anti-cheating property is not that the task itself is
different for every student. The workflow stays identical, while the
submitted secret values differ per sandbox. A copied answer from another
student therefore fails.

With seven generated values per sandbox and 30 sandboxes, the training
has 210 sandbox-specific answer slots.

## Hints

The hint penalties are deliberately small compared with the 20-point
total, but enough to distinguish independent solutions from heavily guided
solutions:

- Task 1: -0.5, -1
- Task 2: -0.5, -1
- Task 3: -1, -1.5
- Task 4: -0.5, -1
- Task 5: -0.5, -1
- Task 6: -1, -1.5
- Task 7: -0.5, -1

## Verification

Before using this with students, allocate one test sandbox and verify:

- `attacker` opens through GUI.
- `www.seed-server.com` resolves from attacker.
- Elgg login works.
- Alice's browser receives the lab cookies.
- The supplied Elgg XSS modifications are active.
- The CSP experiment pages load.
- APG variables are rendered into the provisioning files.
- The training's generated answer names exactly match the variables in
  `variables.yml`.

The exact Elgg action URLs/parameters can vary with the Elgg build. The
student task intentionally asks them to discover those values with the
Network panel, which is also the method prescribed by the supplied TP.
