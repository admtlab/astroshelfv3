/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package service;

import entity.PrefQT;
import java.util.List;
import javax.ejb.Stateless;
import javax.persistence.EntityManager;
import javax.persistence.PersistenceContext;
import javax.ws.rs.*;
import javax.ws.rs.core.Response;

/**
 *
 * @author roxy
 */
@Stateless
@Path("prefqt")
public class PrefQTFacadeREST extends AbstractFacade<PrefQT> {
    @PersistenceContext(unitName = "astroservicePU")
    private EntityManager em;

    public PrefQTFacadeREST() {
        super(PrefQT.class);
    }

    @POST
    @Override
    @Consumes({/*"application/xml",*/ "application/json"})
    public void create(PrefQT prefQT) {
        super.create(prefQT);
        getEntityManager().flush();
        //getEntityManager().refresh(prefQT);
        //getEntityManager().persist(prefQT.getPrefAnnotationCollection());
        //getEntityManager().flush();
        
        //getEntityManager().refresh(prefQT.getPrefAnnotationCollection());
        
    }
    
    @POST
    @Path("add")
    @Consumes({/*"application/xml",*/ "application/json"})
    public Response createAll(PrefQT entity) {
        
        super.create(entity);
        PrefQT result=entity;
        return Response.status(Response.Status.OK).entity(result).header("Access-Control-Allow-Origin", "*").build();

    }
           
    
    @PUT
    @Override
    @Consumes({"application/xml", "application/json"})
    public void edit(PrefQT entity) {
        super.edit(entity);
    }

    @DELETE
    @Path("{id}")
    public void remove(@PathParam("id") Long id) {
        super.remove(super.find(id));
    }

    @GET
    @Path("{id}")
    @Produces({/*"application/xml",*/ "application/json"})
    public PrefQT find(@PathParam("id") Long id) {
        return super.find(id);
    }

    @GET
    @Override
    @Produces({/*"application/xml",*/ "application/json"})
    public List<PrefQT> findAll() {
        return super.findAll();
    }

    @GET
    @Path("{from}/{to}")
    @Produces({"application/xml", "application/json"})
    public List<PrefQT> findRange(@PathParam("from") Integer from, @PathParam("to") Integer to) {
        return super.findRange(new int[]{from, to});
    }

    @GET
    @Path("count")
    @Produces("text/plain")
    public String countREST() {
        return String.valueOf(super.count());
    }

    @java.lang.Override
    protected EntityManager getEntityManager() {
        return em;
    }
    
}
