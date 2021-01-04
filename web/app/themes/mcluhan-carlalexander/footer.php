			<footer class="site-footer section-inner">

				<div class="copyright">
                    <p>&copy; <?php echo date_i18n(__('Y', 'mcluhan')); ?> Carl Alexander</p>
                    <p class="ymir-powered"><strong>This site runs without a server thanks to <a href="https://ymirapp.com"><svg style="height: 12px; width: auto;" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:cc="http://creativecommons.org/ns#" xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#" xmlns:svg="http://www.w3.org/2000/svg" xmlns="http://www.w3.org/2000/svg" xmlns:sodipodi="http://sodipodi.sourceforge.net/DTD/sodipodi-0.dtd" xmlns:inkscape="http://www.inkscape.org/namespaces/inkscape" id="svg6" xml:space="preserve" viewBox="0 0 200 50.201199" y="0px" x="0px" version="1.1" width="200" height="50.201199"><sodipodi:namedview pagecolor="#ffffff" bordercolor="#666666" borderopacity="1" objecttolerance="10" gridtolerance="10" guidetolerance="10" id="namedview1025" showgrid="false"/><metadata id="metadata12"><rdf:RDF><cc:Work rdf:about=""><dc:format>image/svg+xml</dc:format><dc:type rdf:resource="http://purl.org/dc/dcmitype/StillImage" /><dc:title></dc:title></cc:Work></rdf:RDF></metadata><defs id="defs10" /><path id="path4" d="m 63.87057,0 v 50.2012 l 8.38902,-6.52439 V 17.64353 l 16.71158,13.04951 16.77876,-13.04951 v 26.03328 l 8.32256,6.52439 V 0 L 88.97117,19.57463 Z M 0,0.1994 20.37365,27.03169 v 23.10305 h 8.32256 V 27.03169 L 50.00181,0.1994 H 38.88267 L 25.03414,18.70914 10.18646,0.1994 Z m 133.52863,0 v 49.93534 l 8.25609,-6.45793 V 0.1994 Z m 27.65517,0 v 8.32255 h 24.03571 c 1.0209,0.0444 1.93043,0.31077 2.7294,0.79903 0.71019,0.39948 1.35406,1.04335 1.93109,1.93109 0.57703,0.88774 0.86549,2.13061 0.86549,3.72853 0,1.64232 -0.28846,2.90726 -0.86549,3.795 -0.57703,0.88774 -1.2209,1.53089 -1.93109,1.93037 -0.79897,0.48826 -1.7085,0.75464 -2.7294,0.79903 H 161.1838 v 8.25609 h 18.50902 l 11.05267,20.37365 H 200 L 188.88159,29.76109 c 0.22193,0 0.86508,-0.19942 1.93037,-0.59891 1.10967,-0.39948 2.26422,-1.13239 3.46267,-2.19767 1.24283,-1.10968 2.35251,-2.6182 3.32902,-4.52684 0.97651,-1.95302 1.4644,-4.43875 1.4644,-7.45707 0,-1.86425 -0.19943,-3.4846 -0.59891,-4.8606 -0.39948,-1.42038 -0.93224,-2.64117 -1.59805,-3.66207 -0.62141,-1.0209 -1.35432,-1.88627 -2.19767,-2.59646 -0.84335,-0.71019 -1.68665,-1.30919 -2.53,-1.79745 -2.0418,-1.10967 -4.34946,-1.73146 -6.92391,-1.86462 z" fill="#19191e" style="fill:#19191e;fill-opacity:1;stroke-width:0.369891" /></a></strong></p>
                </div>
				<div class="theme-by">
                    <a rel="license" href="http://creativecommons.org/licenses/by-nc-sa/4.0/"><img alt="Creative Commons License" style="border-width:0" src="https://i.creativecommons.org/l/by-nc-sa/4.0/88x31.png" /></a>
                </div>

			</footer> <!-- footer -->

		</main>

		<?php wp_footer(); ?>

        <?php if (is_single()): ?>
            <script>
                window.onload = function() {
                    document.querySelectorAll('pre').forEach((block) => {
                        hljs.highlightBlock(block);
                    });
                };
            </script>
        <?php endif; ?>
	</body>
</html>
