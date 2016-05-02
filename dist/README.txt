--On local machine

cp ~/NetBeansProjects/roxana/astroservice/dist/astroservice.war ~/NetBeansProjects/astro-demo/dist/astroservice.war

--On astro

sudo cp /www/html/panickos/dist/astroservice.war /u/opt/glassfish-3.1.2/glassfish/domains/domain1/autodeploy/
sudo /u/opt/glassfish-3.1.2/glassfish/bin/asadmin redeploy --name astroservice /u/opt/glassfish-3.1.2/glassfish/domains/domain1/autodeploy/astroservice.war

default username: admin
default password: adminadmin
