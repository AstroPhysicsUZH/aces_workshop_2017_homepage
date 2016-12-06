# aces_workshop_2017_homepage
ACES Workshop 2017 Hopepage

## initial setup

```bash
git clone -b init_hook https://github.com/AstroPhysicsUZH/aces_workshop_2017_homepage.git .
echo "<?php\n\$hookSecret='`openssl rand -hex 6`';\n?>" > hook/secret.php
head -n 2 hook/secret.php | tail -1
chmod -R 777 .git hook
```

and create a new webhook in github

Payload URL:
http://www.physik.uzh.ch/events/aces2017/hook/update_hook.php

Content type:
application/json

Secret:
the hookSecret that is in the file secret.php `head -n 2 test.txt | tail -1`

Which events:
Just push
