# Acesso externo ao Jitsi via HestiaCP (1 IP público)

Como a CISCOPAR tem **um único IP público** e o firewall encaminha o `443` para o
servidor **HestiaCP**, o `meet.ciscopar.com.br` é desviado para a VM do Jitsi
(`172.18.100.100`) por um **template de proxy nginx** no Hestia. A mídia (UDP) vai
direto para a VM, sem passar pelo Hestia.

## Componentes

| Canal | Caminho |
|---|---|
| Web + sinalização (TCP 443) | IP público → firewall → **Hestia** → (proxy `jitsi.stpl`) → Jitsi VM `:443` |
| Mídia (UDP 10000) | IP público → firewall (DNAT) → **direto** Jitsi VM `:10000` |
| Mídia fallback (TCP 4443) | IP público → firewall (DNAT) → **direto** Jitsi VM `:4443` |

## Instalação dos templates (na VM Hestia)

```bash
cp jitsi.stpl jitsi.tpl /usr/local/hestia/data/templates/web/nginx/
# domínio meet.ciscopar.com.br: ativar SSL (wildcard) e Proxy Template = jitsi
v-change-web-domain-proxy-tpl USUARIO meet.ciscopar.com.br jitsi
v-rebuild-web-domains USUARIO
```

## Pré-requisitos de rede

- **Cloudflare:** `meet.ciscopar.com.br` = A record → IP público, **DNS-only (nuvem cinza)**.
  O CF não roteia UDP; se ficar laranja, a mídia não passa.
- **Firewall:** DNAT de **UDP 10000** e **TCP 4443** direto para `172.18.100.100`.
- **DNS interno:** `meet.ciscopar.com.br` → `172.18.100.100` (acesso interno não passa pelo Hestia).

## NAT harvester do JVB (na VM Jitsi)

Para o JVB anunciar o IP público nos candidatos ICE — em `/etc/jitsi/videobridge/jvb.conf`:

```hocon
videobridge {
    ice {
        nat-harvester {
            local-address = "172.18.100.100"
            public-address = "SEU_IP_PUBLICO"
        }
    }
}
```
Depois: `systemctl restart jitsi-videobridge2`.
(O `install-jitsi.sh` faz isso automaticamente se `JVB_LOCAL_IP`/`JVB_PUBLIC_IP` estiverem no `jitsi.conf`.)
