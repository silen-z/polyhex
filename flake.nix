{
  description = "experimental PHP framework";
  inputs = {
    nixpkgs.url = "github:NixOS/nixpkgs/nixpkgs-unstable";
    flake-utils.url = "github:numtide/flake-utils";
  };
  
  outputs = { self, nixpkgs, flake-utils, ... }:
    flake-utils.lib.eachDefaultSystem
      (system:
        let
          pkgs = (import nixpkgs) {
            inherit system;
          };

          php = pkgs.php.buildEnv {
            extensions = { enabled, all, ... }: enabled ++ [ all.xdebug ];
            extraConfig = ''
                memory_limit = 2G
                xdebug.mode = debug
            '';
          };
        in
        {
          devShells = {
            default = pkgs.mkShell {
              buildInputs = [ 
                php
                php.packages.composer
                pkgs.just
              ];
            };
          };
        }
      );
}