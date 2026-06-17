				<ul>
					<li><a href="/"><img src="/content/img/home_icon.png" />Home</a></li>
			<?php	IF($SC <> "7") { ?>
                    <li><a href="<?php ECHO $gstrPHPSELF;?>?SC=<?php ECHO $SC;?>"
			<?php 
					IF(!$SD)
					{
						ECHO "style='font-weight:bold;'";
					}
			?>
					><?php ECHO $strTitleM?></a></li>
			<?php	} ?>
			<?php
					FOR($j=0;$j<COUNT($strSubGnb);$j++)
					{
						ECHO "<li><a href='".$strSubGnb[$j][0]."?SC=".$SC."&SD=".$strSubGnb[$j][1]."' style='color:#333333;";
						IF($strSubGnb[$j][1] == $SD)
						{
							ECHO "font-weight:bold;";
						}
						ECHO "'>".$strSubGnb[$j][2]."</a></li>";
					}
			?>
				</ul>