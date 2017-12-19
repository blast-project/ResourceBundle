===================
BlastResourceBundle
===================

Basic resource management for Blast-based applications.


Declaring your resources
========================

The ResourceInterface
---------------------

The ResourceInterface has to be implemented by your model class to be typed as a manageable resource.

.. code-block:: php

    <?php

    namespace AcmeBundle\Entity;

    use Blast\Component\Resource\Model\ResourceInterface;

    class Contact implements ResourceInterface
    {

    }

Configuration
-------------

You can now declare your resource as follows:

.. code-block:: yaml

    # blast/resource.yml

    blast_resource:
        resources:
            contact: #the resource alias
                classes:
                    model: AcmeBundle\Entity\Contact
                    repository: AcmeBundle\Repository\ContactRepository


What is done
------------

Once your resource is declared :

- Parameters are created for each classes using the following format: "%[app].[type].[name].class%"
- The repository is declared as a service using the following format: [app].repository.[name]
- The repository is injected as the default doctrine repository (reachable from the Doctrine EntityManager)
