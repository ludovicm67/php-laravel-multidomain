{ pkgs ? import <nixpkgs> { } }:

let
  php = pkgs.php84.buildEnv {
    extensions = { enabled, all }:
      with all; [
        xdebug
        dom
        mbstring
        tokenizer
        xmlwriter
        simplexml
        filter
        openssl
        ctype
        curl
      ];
  };

in pkgs.mkShell {
  nativeBuildInputs = with pkgs; [ git php php.packages.composer ];

  shellHook = ''
    export XDEBUG_MODE=coverage
  '';
}
