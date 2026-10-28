# CyberRangeCZ — SEED XSS / Elgg Security Lab

This repository is a CyberRangeCZ sandbox definition + linear training
definition adapted from the supplied SEED Labs Cross-Site Scripting Attack
Lab PDF and the supplied `library-junior-hacker` CyberRange scenario.

## Topology

- `attacker`: SEED Linux, `m1.medium`, user-accessible.
- `web-server`: SEED Linux, `m1.medium`, on a hidden target network.
- `router`: Debian router node.
- Elgg + MySQL run as Docker containers on `web-server`.
- HTTP port 80 from the Elgg container is published on `web-server`.

The attacker reaches the web application at:

`http://www.seed-server.com`

The provisioning adds the required `/etc/hosts` entries.

## 30-student anti-cheating design

The training is an APG training. `variables.yml` defines seven generated
variables:

- `task1_flag`
- `task2_flag`
- `task3_flag`
- `task4_flag`
- `task5_flag`
- `task6_flag`
- `task7_flag`

CyberRangeCZ generates a different value for each sandbox and stores the
answers for that sandbox. Therefore, a pool of 30 sandboxes gives each of
the 30 trainees a different seven-flag answer set.

Create a pool with **30 sandboxes** for a 30-student session.

## Marking: 20/20

| Task | Topic | Points |
|---|---|---:|
| 1 | Stored XSS execution | 3 |
| 2 | Cookie access | 3 |
| 3 | HTTP exfiltration | 4 |
| 4 | Forged friend request | 3 |
| 5 | Profile modification | 3 |
| 6 | Self-propagating DOM worm | 2 |
| 7 | CSP | 2 |
| **Total** | | **20** |

Hints use penalties between **-0.5 and -2 points**, as requested.

The normal CyberRangeCZ solution button remains available to the instructor;
revealing a solution is configured as a full-level penalty.

## Deployment

1. Create/push this directory as a Git repository.
2. Register the repository as a CyberRangeCZ Sandbox Definition.
3. Allocate a pool of 30 sandboxes.
4. Register/upload `training.json` as a Linear Training Definition.
5. Enable APG / variant sandbox generation for the training.
6. Assign the 30-sandbox pool to the training instance.

The supplied platform documentation describes APG as using `variables.yml`
to generate per-sandbox values and the training level's "Correct Answer -
Variable Name" to reference those variables.

## Notes

The original SEED Lab source files and license/copyright notices supplied
by the user are retained inside `provisioning/roles/server/files/Labsetup`.
The scenario text itself is rewritten rather than copying the PDF verbatim.

The exercise is intentionally isolated and should only be run inside the
CyberRange sandbox.
