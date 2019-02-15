<?php

namespace Kartavik\PHPMock;

/**
 * Implementation deactivates related mocks.
 *
 * @author Markus Malkusch <markus@malkusch.de>
 * @author Roman Varkuta <roman.varkuta@gmail.com>
 */
interface Deactivatable
{

    /**
     * Disable related mocks.
     */
    public function disable();
}
