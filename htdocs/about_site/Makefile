all: daily export

daily:
	rm -f webfinance_HEAD.tar.gz

export: webfinance_HEAD.tar.gz webfinance_RELEASE_0_1.tar.gz

%.tar.gz: 
	cvs -d :ext:ovh.isvtec.com:/var/lib/cvs export -r `echo $@ | sed -e s/webfinance_// -e s/.tar.gz//` webfinance
	tar -zcf $@ webfinance
	rm -rf webfinance
