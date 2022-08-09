# Doko Zip Compressor

1. If we add multiple archiving methods (7z, for example);

I created Interface for that, so there would be an easy approach to attach new compressor types.

2. Face a significant increase in request count;

Haven't made, but the usage of Load Balancer in ngnix would help out, also cachable files.

3. Allow 1GB max file size;

Haven't made, but to make it quicker, multiple upload streams, distributed storage.
