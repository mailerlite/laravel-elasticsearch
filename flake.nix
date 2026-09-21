{
  description = "laravel-elasticsearch dev environment";

  inputs = {
    nixpkgs.url = "github:NixOS/nixpkgs/nixos-unstable";
  };

  outputs = { self, nixpkgs }:
    let
      systems = [ "aarch64-darwin" "x86_64-darwin" "aarch64-linux" "x86_64-linux" ];
      forEachSystem = f:
        nixpkgs.lib.genAttrs systems (system: f system nixpkgs.legacyPackages.${system});
    in
    {
      devShells = forEachSystem (system: pkgs: {
        default = pkgs.mkShell {
          packages = with pkgs; [
            lefthook
            gitleaks
          ];
        };
      });
    };
}
