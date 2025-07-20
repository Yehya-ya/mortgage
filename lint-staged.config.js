export default {
    "**/*.php*": [
        "vendor/bin/duster fix --dirty"
    ],
    '**/*': 'prettier --write --ignore-unknown',
}
