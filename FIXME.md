cd ~/projets/htdocs/adullact/webrsa/tests/web-rsa-trunk-composer && \
vendors/bin/phpcs \
	--standard=app/Contrib/phpcs/Cake2CodesnifferParanoid/ruleset.xml \
	--report=checkstyle \
	app