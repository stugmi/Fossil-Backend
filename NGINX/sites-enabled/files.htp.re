# We want to only allow connections from Cloudflare, any other IP will return false.
# This will later be used to return a 444 to visitors not using CF.

geo $realip_remote_addr $cloudflare_ip {
    default          0;
    103.21.244.0/22  1;
    103.22.200.0/22  1;
    103.31.4.0/22    1;
    104.16.0.0/12    1;
    108.162.192.0/18 1;
    131.0.72.0/22    1;
    141.101.64.0/18  1;
    162.158.0.0/15   1;
    172.64.0.0/13    1;
    173.245.48.0/20  1;
    188.114.96.0/20  1;
    190.93.240.0/20  1;
    197.234.240.0/22 1;
    198.41.128.0/17  1;
    199.27.128.0/21  1;
    2400:cb00::/32   1;
    2405:8100::/32   1;
    2405:b500::/32   1;
    2606:4700::/32   1;
    2803:f800::/32   1;
    2c0f:f248::/32   1;
    2a06:98c0::/29   1;
}

server {

	# Deferred stands for TCP_DEFER_ACCEPT.
	listen [::]:80 deferred;
	listen 80 deferred;
	server_name files.htp.re;

	root /sites/hosting/;
	index index.html index.htm index.php;

	client_max_body_size 100G;

	# Including misc
	include h5bp/basic.conf;
	include money/basic.conf;

	# If geo block returns 0 return 444 (no response)
	# This prevents people from connecting to our site without using CF as a middleman
	if ($cloudflare_ip != 1) {
	    return 444;
	}

	error_log /sites/logs/error_log;
	access_log /sites/logs/access_log;


}

server {

	listen 80;
	server_name f.htp.re;
	root /sites/files;

	access_log /sites/logs/files_log;
}
