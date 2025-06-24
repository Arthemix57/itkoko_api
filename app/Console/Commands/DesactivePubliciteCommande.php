<?php

namespace App\Console\Commands;

use App\Models\Publicite;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class DesactivePubliciteCommande extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'DesactivePubliciteCommande:desactivepublicitecommande';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Commande pour desactiver les publicitées dont la date de fin est dépassée';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Logic to retrieve and return all publicities that are not active
        $publicites = Publicite::where('is_active', true)->get(); // Example of retrieving all non-active publicities

        if ($publicites->isEmpty()) {
            $this->error('No active publicities found');
            return;
        }

        //En Laravel 10, la comparaison de deux dates de type datetime peut être réalisée en utilisant la classe Carbon, qui est intégrée dans Laravel pour la manipulation des dates. Vous pouvez comparer les dates en utilisant des méthodes telles que eq(), ne(), gt(), gte(), lt(), et lte() pour vérifier l'égalité, l'inégalité, la supériorité, la supériorité ou l'égalité, l'infériorité, et l'infériorité ou l'égalité respectivement. 

        foreach ($publicites as $key => $value) {
            if (Carbon::parse($value->date_fin)->lte(Carbon::parse(Carbon::now()))) {
                $value->is_active = false; // Set the publicity as inactive if the start date is equal to the end date
                $value->update();
                $this->info("Publicity with ID {$value->id} has been deactivated.");
            }
        }

         // Here you would typically return the publicities from the database
        $this->info('List of active publicities has been updated.');
    }
}
