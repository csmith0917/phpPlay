<footer>
    <!-- <p>&copy; 2006&ndash;<?php echo date('Y'); ?> David Powers</p> -->
    <p>&copy; 
    	<?php 
    		$startYear = 2015;
    		//$thisYear = date('Y');
    		if ($startYear == 2015) {
    			echo $startYear;
    		} else {
    			echo "{$startYear}&ndash;2015";
    		}
    	 ?>
    	 David Powers
    </p>
</footer>