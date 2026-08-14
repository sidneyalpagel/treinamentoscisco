#=========================================================================#
# Template nginx (proxy) do HestiaCP para o Jitsi — HTTP (redirect 443)    #
# Instale em: /usr/local/hestia/data/templates/web/nginx/jitsi.tpl         #
#=========================================================================#
server {
        listen      %ip%:%proxy_port%;
        server_name %domain_idn% %alias_idn%;

        location ^~ /.well-known/acme-challenge/ {
                root %home%/%user%/web/%domain%/public_html;
        }
        location / { return 301 https://$host$request_uri; }
}
