__________  ____    ___  ___________  __________ _____   ___ __________  ___________  ___________
|___  ____| |   \   | |  |  _______| |____  ___| |    \  | | |____  ___| |____  ____| |  _______|
    | |     | |\ \  | |  |  |______      |  |    | |\  \ | |     | |         |  |     |  |____
    | |     | | \ \ | |  |  _______|     |  |    | | \  \| |     | |         |  |     |  _____|
____| |___  | |  \ \| |  |  |        ____|  |___ | |  \    | ____| |___      |  |     |  |______
|_________| |_|   \___|  |__|        |_________| |_|   \___| |_________|     |__|     |_________|

___________ ____    ___  ___________
| _________||   \  /   | |  _______|
| |         | |\ \/ /| | |  |______
| |         | | \__/ | | |_______  |
| |________ | |      | |  ______|  |
|__________||_|      |_| |_________|


##LE CMS ~ De vos Rêves


 - Vous ne pouvez pas vendre ce CMS
  You can't sell this CMS
 - Vous pouvez distribuer vos thèmes
  You can release your templates
 - Vous ne pouvez pas vendre vos thèmes
  You can not sell your templates
 - Vous pouvez distribuer vos traductions
  You can release your translations
 - Vous pouvez modifier ce CMS, mais vous ne devez pas le distribuer à votre nom
  You can modify the CMS, but you can't release your modifications as it's your CMS
 - Le thème DOIT être changé (aucun support si le thème n'est pas changé)
  The template MUST be changed (if template is not changed, you will get no support)

Do not upload:
Ne pas envoyer:
 - changelog.txt
 - doctrine.php
 - doctrine.bat
 - readme

# Problème courants
Que faire en premier ?
Tout d'abord, de base, vous devriez avoir un de ces messages :
_Problems during the init. Please contact the server admin._
_Problems during page loading. Please contact the server admin._
Il faut activer le mode debug. Ce mode est activé par défaut si vous accédez au CMS via localhost/. Si vous ne pouvez pas, vous pouvez le forcer.
Pour faire ceci, ouvrez /index.php et après `<?php`, ajoutez
```php
define('DEBUG', true);
```