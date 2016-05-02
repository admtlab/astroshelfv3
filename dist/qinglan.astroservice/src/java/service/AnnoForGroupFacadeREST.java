/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package service;

import entity.AnnoForGroup;
import java.util.List;
import javax.ejb.Stateless;
import javax.persistence.EntityManager;
import javax.persistence.PersistenceContext;
import javax.ws.rs.*;

/**
 *
 * @author roxy
 */
@Stateless
@Path("entity.annoforgroup")
public class AnnoForGroupFacadeREST extends AbstractFacade<AnnoForGroup> {
    @PersistenceContext(unitName = "astroservicePU")
    private EntityManager em;

    public AnnoForGroupFacadeREST() {
        super(AnnoForGroup.class);
    }

    @POST
    @Override
    @Consumes({"application/xml", "application/json"})
    public void create(AnnoForGroup entity) {
        super.create(entity);
    }

    @PUT
    @Override
    @Consumes({"application/xml", "application/json"})
    public void edit(AnnoForGroup entity) {
        super.edit(entity);
    }

    @DELETE
    @Path("{id}")
    public void remove(@PathParam("id") Long id) {
        super.remove(super.find(id));
    }

    @GET
    @Path("{id}")
    @Produces({"application/xml", "application/json"})
    public AnnoForGroup find(@PathParam("id") Long id) {
        return super.find(id);
    }

    @GET
    @Override
    @Produces({"application/xml", "application/json"})
    public List<AnnoForGroup> findAll() {
        return super.findAll();
    }

    @GET
    @Path("{from}/{to}")
    @Produces({"application/xml", "application/json"})
    public List<AnnoForGroup> findRange(@PathParam("from") Integer from, @PathParam("to") Integer to) {
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
